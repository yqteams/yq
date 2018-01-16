<?php

namespace YQ;

class YqExtend
{
    /**
     * 下划线转驼峰
     * @param  string $str 下划线字符串
     * @return string      驼峰字符串
     */
    public static function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
            return strtoupper($matches[2]);
        },$str);
        return $str;
    }

    /**
     * 驼峰转下划线
     * @param  string $str 驼峰字符串
     * @return string      下划线字符串
     */
    public static function humpToLine($str)
    {
        $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$str);
        return $str;
    }

    /**
     * 16位md5
     * @param  string $str 待签名md5
     * @return string
     */
    public static function md516($str)
    {
        return substr(md5($str),8,16);
    }

    /**
     * 随机拼接字符串
     * @param  integer $len 拼接长度
     * @return string
     */
    public static function getRandom($len=16)
    {
        $allstr = "0123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
        $len = 16;
        $str = '';
        $max = strlen($allstr)-1;
        for($i=0;$i<=$len;++$i){
            $str .= $allstr[mt_rand(0,$max)];
        }
        return $str;
    }

    /**
     * 随机拼接字母
     * @param  integer $len 拼接长度
     * @return string
     */
    public static function getRandomLetter($len=16)
    {
        $allstr = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
        $str = '';
        $max = strlen($allstr)-1;
        for($i=0;$i<$len;++$i){
            $str .= $allstr[mt_rand(0,$max)];
        }
        return $str;
    }

    /**
     * 随机拼接数字
     * @param  integer $len 拼接长度
     * @return string
     */
    public static function getRandomInt($len=16)
    {
        $allstr = "0123456789";
        $str = '';
        $max = strlen($allstr)-1;
        for($i=0;$i<$len;++$i){
            $str .= $allstr[mt_rand(0,$max)];
        }
        return $str;
    }

    /**
     * 创建一个32位唯一值 可用于临时票据,订单等
     * @param  string $id 标识
     * @return string
     */
    public static function uniqid32($id = 'uniqid')
    {
        return md5($id . time() . self::getRandom() . uniqid());
    }

    /**
     * 得到一个唯一值，可用于订单等，此接口可视化强，但高并发时有极小的概率出现相同值
     * 标识(4位) 20180105 231106 013118(20位) 随机数字(8位) 补全到32位
     * @param  integer $flag 标识
     * @return string
     */
    public static function uniqid($flag=1000)
    {
        $num = floatval(microtime()) * 1000000;
        $id = $flag . date('YmdHis') . str_pad($num, 6, "0", STR_PAD_LEFT);
        $diff = 32;
        if (strlen($id)<$diff) {
            $id .= self::getRandomInt($diff-strlen($id));
        }
        return $id;
    }

    /**
     * 创建16位订单号
     * uniqid获取一个基于当前的微秒数生成的唯一不重复的字符串（但是他的前7位貌似很久才会发生变动，所以不用考虑可删除），
     * 取其第8到13位。但是这个字符串里面有英文字母，用ord获取他的ASCII码，
     * 但是最终得出字符长度不定，取前固定的几位，然后前面加上当前的年份和日期，
     * 最后检查长度是否达到16位，不够则补随机数
     *
     * 这个方法生成的订单号，出现重复极少，可忽略，市面上不少技术采用此方式进行创建16位订单
     * @return int
     */
    public static function buildOrderNo()
    {
        $id = date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 10);
        $diff = 16;
        if (strlen($id)<$diff) {
            $id .= self::getRandomInt($diff-strlen($id));
        }
        return $id;
    }

    /**
     * 根据当前时间获取排序值，时间越大，排序越小
     * @return integer
     */
    public static function sortToTime()
    {
        return strtotime('2100-01-01') - time();
    }

    /**
     * 获取客户端IP
     * @return string
     */
    public static function getIP()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $thisip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $thisip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $thisip = $_SERVER["REMOTE_ADDR"];
            }
        }
        if (strpos($thisip, "10.0.0.") !== false ||
            strpos($thisip, "192.168.") !== false ||
            strpos($thisip, "127.0.0.") !== false ||
            strpos($thisip, "172.16.0.") !== false) {
                $thisip = $_SERVER["REMOTE_ADDR"];
        }

        return $thisip;
    }

    /**
     * 获取服务端ip
     * @return string
     */
    public static function getServerIp()
    {
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        } else {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

    /**
     * 获取当前访问url，包括get参数 http://url?xxx
     * @return [type] [description]
     */
    public static function currentUrl()
    {
        $protocol = 'http://';

        if (!empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') === 'https') {
            $protocol = 'https://';
        }

        return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}
