<?php

namespace YQ\Upload;

use YQ\YqExtend;
use Intervention\Image\ImageManagerStatic as Image;

class UploadBase
{
    /**
     * 文件存储驱动
     * @var string
     */
    protected $driver = 'YQ\\Upload\\Drivers\UploadDriverLocal';

    /**
     * 缓存驱动实例化对象
     * @var obj
     */
    protected $driverObj;

    /**
     * 缓存驱动配置
     * @var array
     */
    protected $driverParams = [];

    /**
     * 上传名(前端html file标签的名称)
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
    protected $uploadPath = '/tmp';

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

    public function __construct()
    {
        $class = $this->driver;
        $this->driverObj = new $class($this->driverParams);
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

        if (strpos($this->allowedTypes, strtolower($ext)) !== false) {
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
        $img_mimes = ['gif', 'jpeg', 'png', 'jpg', 'gpeg', 'image/gif', 'image/png', 'image/jpeg'];
        return in_array(strtolower($ext), $img_mimes, TRUE);
    }

    /**
     * 获取文件扩展名
     * @param  string $filename 文件名
     * @return string
     */
    private function getExtension($filename)
    {
        $x = explode('.', $filename);

        if (count($x) === 1)
        {
            return '';
        }

        return end($x);
    }

    /**
     * 上传内容检查
     * @param  array $data 由initData接口产生数据
     * @return boolean
     */
    private function check($data)
    {
        // 校验mime类型
        if (!$this->isAllowedFiletype($data['origina_ext'])) {
            return 'check allowed file type error';
        }
        // 校验文件大小
        if (($data['file_size']/1024)>$this->maxSize) {
            return 'check file size error';
        }

        // 校验图片的长宽
        if ($data['is_image']) {
            if ($data['image_width']<$this->minWidth || $data['image_width']>$this->maxWidth) {
                return 'check image width error';
            }
            if ($data['image_height']<$this->minHeight || $data['image_height']>$this->maxHeight) {
                return 'check image height error';
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
        $data = [
            'origina_name' => $file['name'], // 原文件名
            'origina_type' => $file['type'], // 原文件类型
            'origina_ext'  => $this->getExtension($file['name']), // 原文件扩展名
            'real_path'    => $file['tmp_name'], // 缓存在tmp文件夹下的文件的绝对路径
            'file_size'    => $file['size'], // 文件大小 字节计
            'is_image'     => false, // 是否为图片
        ];

        if ($this->isImage($data['origina_type'])) {
            $imagesizes = getimagesize($data['real_path']);
            $data['is_image'] = true;
            $data['image_width'] = $imagesizes[0]; // 图片的宽
            $data['image_height'] = $imagesizes[1]; // 图片的高
        }

        return $data;
    }

    protected function save($data)
    {
        $ret = [];

        // 存放文件目录
        $path = YqExtend::uniqid();
        if (!$this->uploadPath == '') {
            $path = $this->uploadPath."/{$path}";
        }

        $tmp               = $data;
        $tmp['save_path']  = $path;
        $tmp['save_name']  = '0.'.$data['origina_ext'];
        $tmp['dirver_ret'] = $this->driverObj->save($path, $tmp['save_name'], $data['real_path']);
        $ret[0] = $tmp;

        // 如果是图片，则进行裁剪
        if ($data['is_image']) {
            if (empty($this->trimSizes)) {
                return $ret;
            }
            $real_path = $data['real_path'];
            foreach ($this->trimSizes as $size) {
                $img = Image::make($real_path);
                $img->fit($size);
                $tmp_file = "{$real_path}_{$size}";
                $img->save($tmp_file);

                $tmp                 = $data;
                $tmp['image_width']  = $size;
                $tmp['image_height'] = $size;
                $tmp['file_size']    = filesize($tmp_file);
                $tmp['save_path']    = $path;
                $tmp['save_name']    = "{$size}.".$data['origina_ext'];
                $tmp['dirver_ret']   = $this->driverObj->save($path, $tmp['save_name'], $tmp_file);
                $ret[$size]          = $tmp;

                @unlink($tmp_file);
            }
        }

        return $ret;
    }

    /**
     * 外部接口 上传处理
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

        $file = $_FILES[$field];

        // 判断是否有错误
        if ($file['error'] !== 0) {
            return [false, 'upload error:'.$file['error']];
        }

        // 判断指定的文件是否是通过 HTTP POST 上传的
        if (!is_uploaded_file($file['tmp_name'])) {
            return [false, 'is uploaded file error'];
        }

        // 初始化数据
        $init_data = $this->initData($file);

        // 校验参数
        $ret = $this->check($init_data);
        if ($ret !== true) {
            return [false, $ret];
        }

        $save_data = $this->save($init_data);

        return [true, $save_data];
    }
}
