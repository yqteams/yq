<?php

namespace YQ;

class YqValidator
{
    /**
     * 校验邮箱格式
     * @param  string $str 字符串
     * @return boolean
     */
    public static function email($str)
    {
        $reg = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z_\-]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if (preg_match($reg, $str)) {
            return true;
        }

        return false;
    }

    /**
     * 校验手机号码格式
     * @param  string $phone 字符串
     * @return boolean
     */
    public static function phone($phone)
    {
        if (empty($phone) || !is_numeric($phone) || strlen($phone) !== 11 || substr($phone, 0, 1) !== '1') {
            return false;
        }

        return true;
    }

    /**
     * 校验身份证格式
     * @param  string $str 字符串
     * @return boolean
     */
    public static function card($str)
    {
        $reg = "/^\d{15}$|^\d{17}(\d|X|x)$/";
        if (preg_match($reg, $str)) {
            return true;
        }

        return false;
    }

    /**
     * 判断日期格式
     * @param        $str
     * @param string $format 验证的格式
     * @return bool
     */
    public static function date($str, $format = "Y-m-d")
    {
        $strArr = explode("-", $str);
        if (empty($strArr)) {
            return false;
        }
        foreach ($strArr as $val) {
            if (strlen($val) < 2) {
                $val = "0" . $val;
            }
            $newArr[] = $val;
        }
        $str       = implode("-", $newArr);
        $unixTime  = strtotime($str);
        $checkDate = date($format, $unixTime);
        if ($checkDate == $str) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 判断账号是否合法
     * @param     $val
     * @param int $minlen 账号最小长度，
     * @param int $maxlen 账号最大长度
     * @return bool
     */
    public static function username($val, $minlen = 6, $maxlen = 14)
    {
        //长度限制
        $len = strlen($val);
        if ($len > $maxlen || $len < $minlen) {
            return false;
        }

        //只允许 字母数字下划线.并且首字符不能是数字
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $val)) {
            return false;
        }
        return true;
    }

    /**
     * 判断密码
     * @param     $val
     * @param int $minlen 密码最小长度，
     * @param int $maxlen 密码最大长度
     * @return bool
     */
    public static function password($val, $minlen = 6, $maxlen = 16)
    {
        $len = strlen($val);
        if ($len > $maxlen || $len < $minlen) {
            return false;
        }

        //第一个到时候可以该为[a-zA-Z_]不允许数字开头
        if (!preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_]+$/", $val)) {
            return false;
        }

        return true;
    }

    /**
     * 判断特殊字符
     * @param $val
     * @return bool
     */
    public static function specialChar($val)
    {
        if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $val)) { //不允许特殊字符
            return false;
        }
        return true;
    }
}
