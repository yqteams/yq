<?php

namespace App\Uploads;

use Intervention\Image\ImageManagerStatic as Image;

class UploadBase
{
    /**
     * 文件存储驱动
     * @var string
     */
    protected $drivers = 'test';

    /**
     * 上传名
     * @var string
     */
    protected $fileName = 'file';

    /**
     * 允许上传文件大小的最大值（单位 KB），默认2MB
     * @var int
     */
    protected $maxSize = 2048;

    /**
     * 图片的最大宽度（单位为像素）
     * @var int
     */
    protected $maxWidth = 2048;

    /**
     * 图片的最大高度（单位为像素）
     * @var int
     */
    protected $maxHeight = 2048;

    /**
     * 图片的最小宽度（单位为像素）
     * @var int
     */
    protected $minWidth = 1;

    /**
     * 图片的最小高度（单位为像素）
     * @var int
     */
    protected $minHeight = 1;

    /**
     * 图片裁剪像素集合
     * @var string
     */
    protected $trimSizes = [46,64,96,132,640];

    /**
     * 默认返回图片大小 0为原图
     * @var string
     */
    protected $pullDefaultSize = 0;

    /**
     * 允许上的文件 MIME 类型
     * @var string
     */
    protected $allowedTypes = '*';

    /**
     * 上传文件存放相对路径
     * @var string
     */
    protected $uploadPath = '';

    /**
     * 单例模式
     * @var obj
     */
    private static $_instance = [];

    /**
     * 获取单例实例化对象
     * @return obj
     */
    public static function getInstance()
    {
        $class = static::class;
        if (!isset(self::$_instance[$class])) {
            self::$_instance[$class] = new $class();
        }
        return self::$_instance[$class];
    }

    /**
     * 校验文件类型是否有效
     * @param  string  $ext 文件扩展类型
     * @return boolean
     */
    private function isAllowedFiletype($ext)
    {
        if ($this->allowedTypes === '*') {
            return true;
        }

        if (strpos($this->allowedTypes, $ext) !== false) {
            return true;
        }

        return false;
    }

    /**
     * 校验文件是否为图片
     * @param  string  $ext 文件扩展类型
     * @return boolean
     */
    private function isImage($ext)
    {
        $img_mimes = ['gif', 'jpeg', 'png', 'jpg', 'gpeg'];
        return in_array($ext, $img_mimes, TRUE);
    }

    /**
     * 上传内容检查
     * @param  array $data 由initData接口产生数据
     * @return boolean
     */
    private function check($data)
    {
        // 校验mime类型
        if (!$this->isAllowedFiletype($data['file_ext'])) {
            return;
        }
        // 校验文件大小
        if (($data['file_size']/1024)>$this->maxSize) {
            return;
        }

        // 校验图片的长宽
        if ($data['is_image']) {
            if ($data['image_width']<$this->minWidth || $data['image_width']>$this->maxWidth) {
                return;
            }
            if ($data['image_height']<$this->minHeight || $data['image_height']>$this->maxHeight) {
                return;
            }
        }

        return true;
    }

    /**
     * 初始化数据
     * @param  obj $file laravel file对象
     * @return array
     */
    private function initData($file)
    {
        $this->file_temp = $_file['tmp_name'];
        $this->file_size = $_file['size'];

        $data = [
            'origina_name' => $file->getClientOriginalName(), // 原文件名
            'origina_ext'  => $file->getClientOriginalExtension(), // 原文件扩展名
            'real_path'    => $file->getRealPath(), // 缓存在tmp文件夹下的文件的绝对路径
            'tmp_name'     => $file['tmp_name'], // 缓存在tmp文件中的文件名
            'file_ext'     => $file->extension(), // 根据文件内容判断扩展名
            'file_size'    => $file['size'], // 文件大小 字节计
            'is_image'     => false, // 是否为图片
        ];

        if ($this->isImage($data['file_ext'])) {
            $imagesizes = getimagesize($data['real_path']);
            $data['is_image'] = true;
            $data['image_width'] = $imagesizes[0]; // 图片的宽
            $data['image_height'] = $imagesizes[1]; // 图片的高
        }

        return $data;
    }

    /**
     * 图片裁剪
     * @param  array  $data 由initData接口产生数据
     * @param  string $path 文件相对驱动存放目录
     * @param  string $name 存放文件名
     * @return void
     */
    private function trimImage(array $data, $path, $name)
    {
        if (empty($this->trimSizes)) return;
        $real_path = $data['real_path'];
        foreach ($this->trimSizes as $size) {
            $img = Image::make($real_path);
            $img->fit($size);
            $tmp_file = "{$real_path}_{$size}";
            $img->save($tmp_file);
            $putname = "{$name}_{$size}";
            Storage::disk($this->drivers)->putFileAs($path, new File($tmp_file), $putname);
            @unlink($tmp_file);
        }
    }

    /**
     * 上传处理
     * @param  string $field 上传文件名
     * @return string
     */
    public function doUpload($field = '')
    {
        if ($field == '') {
            $field = $this->fileName;
        }

        if (!isset($_FILES[$field])) {
            return [false, 'can not found field'];
        }

        $_file = $_FILES[$field];

        // 判断指定的文件是否是通过 HTTP POST 上传的
        if (!is_uploaded_file($_file['tmp_name'])) {
            $msg = '';
            $error = isset($_file['error']) ? $_file['error'] : 4;
            switch ($error) {
                case UPLOAD_ERR_INI_SIZE:
                    $msg = 'upload_file_exceeds_limit';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $msg = 'upload_file_exceeds_form_limit';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $msg = 'upload_file_partial';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $msg = 'upload_no_file_selected';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $msg = 'upload_no_temp_directory';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $msg = 'upload_unable_to_write_file';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $msg = 'upload_stopped_by_extension';
                    break;
                default:
                    $msg = 'upload_no_file_selected';
                    break;
            }

            return [false, $msg];
        }

        $data = $this->initData($_file);
    }

    /**
     * 上传处理
     * */
    public function upload($file) {
        $data = $this->initData($file);
        if (!$this->check($data)) {
            return;
        }

        // 存放文件目录和文件名
        $hash_name = $file->hashName();
        $path = str_replace(".".$file->guessExtension(), '', $hash_name);
        if (!$this->uploadPath == '') {
            $path = $this->uploadPath."/{$path}";
        }
        $url = $file->storeAs($path, $hash_name, $this->drivers);

        // 数据落地
        $uploadfile = new UploadFiles;
        $uploadfile->create_time = YQ_REQUEST_TIME;
        $uploadfile->file_ext = $data['file_ext'];
        $uploadfile->file_size = ceil($data['file_size']/1024);
        if ($data['is_image']) {
            $uploadfile->is_image = 1;
            $uploadfile->image_height = $data['image_height'];
            $uploadfile->image_width = $data['image_width'];
            $uploadfile->trim_sizes = json_encode($this->trimSizes);
        }
        $uploadfile->drivers = $this->drivers;
        $uploadfile->class = static::class;
        $uploadfile->save_path = $path;
        $uploadfile->url = $url;
        $uploadfile->pull_default_size = $this->pullDefaultSize;

        $uploadfile->save();

        // 如果是图片，则进行裁剪
        if ($data['is_image']) {
            $this->trimImage($data, $path, $hash_name);
        }

        return $uploadfile->id;
    }
}
