<?php

namespace YQ;

/**
 * google 接口父类
 * 官方php-sdk：https://github.com/google/google-api-php-client
 */
class YqGoogle
{
    /**
     * Google 对象
     * @var Google
     */
    public $google;

    /**
     * 配置信息
     * @var array
     */
    protected $configList;

    /**
     * 功能类实例化
     * @var array
     */
    protected $objList;

    public function __construct(array $config)
    {
        $this->configList = $config;

        $this->google = new \Google_Client([
            'client_id' => $config['client_id'],
        ]);
    }

    /**
     * 读取配置信息
     * @param  string $key 配置key，如果需求获取二维数组里的值，可以使用 key1.key2
     * @param  mixed  $default 如果找不到数据则返回默认值
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        $array = $this->configList;

        if (isset($array[$key])) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (isset($array[$segment])) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * 根据用户登录成功获取到的token，前往google校验
     * https://developers.google.com/identity/sign-in/android/backend-auth?hl=zh-cn
     * @param  string $user_token 前端登录成功获取到的token
     * @return array
     */
    public function verifyIdToken($user_token)
    {
        $payload = $this->google->verifyIdToken($user_token);
        if ($payload) {
            return [true, $payload];
        } else {
            return [false, 'Invalid ID token'];
        }
    }
}
