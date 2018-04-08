<?php

namespace YQ;

class YqRsa
{
    /**
     * rsa公钥
     * @var string
     */
    private $publicKey = "";

    /**
     * rsa私钥
     * @var string
     */
    private $privateKey = "";

    public function __construct($public_key, $private_key)
    {
        $this->publicKey  = $public_key;
        $this->privateKey = $private_key;
    }

    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey()
    {
        $key = $this->privateKey;
        return openssl_pkey_get_private($key);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $key = $this->privateKey;
        return openssl_pkey_get_public($key);
    }

    /**
     * 对字符串数据进行私钥签名
     * @param  string  $data 数据
     * @param  integer $type 签名类型 参考 http://php.net/manual/zh/openssl.signature-algos.php
     * @return string  返回数据经过 base64_encode
     */
    private function opensslSign($data, $type)
    {
        return openssl_sign($data, $sign, self::getPrivateKey(), $type) ? base64_encode($sign) : null;
    }

    /**
     * 对使用openssl_sign秘钥签名的数据进行公钥校验
     * @param  string  $data 数据 进行过 base64_encode
     * @param  string  $sign 签名数据
     * @param  integer $type 签名类型 参考 http://php.net/manual/zh/openssl.signature-algos.php
     * @return boolean
     */
    private function opensslVerify($data, $sign, $type)
    {
        return (bool)openssl_verify($data, base64_decode($sign), self::getPublicKey(), $type);
    }

    /**
     * SHA256WithRSA 根据私钥创建签名
     * @param string $data 数据
     * @return null|string 返回数据经过 base64_encode
     */
    public function sha256WithRSASign($data)
    {
        return $this->opensslSign($data, OPENSSL_ALGO_SHA256);
    }

    /**
     * SHA256WithRSA 使用公钥验证去验证被私钥签名签名的数据。
     * @param string $data 数据
     * @param string $sign 签名
     * @return bool
     */
    public function sha256WithRSAVerify($data, $sign)
    {
        return $this->opensslSign($data, $sign, OPENSSL_ALGO_SHA256);
    }

    /**
     * SHA1WithRSA 根据私钥创建签名
     * @param string $data 数据
     * @return null|string 返回数据经过 base64_encode
     */
    public function sha1WithRSASign($data)
    {
        return $this->opensslSign($data, OPENSSL_ALGO_SHA1);
    }

    /**
     * SHA1WithRSA 使用公钥验证去验证被私钥签名签名的数据。
     * @param string $data 数据
     * @param string $sign 签名
     * @return bool
     */
    public function sha1WithRSAVerify($data, $sign)
    {
        return $this->opensslSign($data, $sign, OPENSSL_ALGO_SHA1);
    }
}
