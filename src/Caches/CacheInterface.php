<?php

namespace YQ\Caches;

interface CacheInterface
{
    /**
     * 是否存在某缓存
     * @param  string   $key       检索key
     * @return boolean
     */
    public function has($key);

    /**
     * 设置缓存数据
     * @param  string   $key        保存的key,操作数据的唯一标识，不可重复
     * @param  mixed    $value      缓存内容
     * @param  integer  $minutes    缓存多少分钟
     * @return boolean
     */
    public function set($key, $value, $minutes);

    /**
     * 读取换成数据
     * @param  string   $key       检索key
     * @return mixed
     */
    public function get($key);

    /**
     * 从缓存中移除项目
     * @param  string  $key    检索key
     * @return boolean
     */
    public function forget($key);

    /**
     * 移除所有缓存
     * @return boolean
     */
    public function flush();
}
