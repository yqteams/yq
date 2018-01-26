<?php

namespace YQ\Elastic;

use YQ\Elastic\ElasticClient;

class ElasticBase
{
    /**
     * 索引(库名)
     * @var string
     */
    protected $_index = 'accounts';

    /**
     * 类型(表名)
     * @var string
     */
    protected $_type = 'person';

    protected $_mapping = [
        'index' => $index,
        'body' => [
            'mappings' => [
                $type => [
                    'properties' => [
                        'first_name' => [
                            'type' => 'string',
                            'analyzer' => 'standard'
                        ],
                        'age' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ]
    ];

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
     * 初始化链接对象
     */
    public function __construct()
    {
        // 判断此索引是否存在，如果不存在则创建

    }

    public function search($key, $value)
    {
        return ElasticClient::getInstance()->searchMatch($this->_index, $this->_type, [$key => $value]);
    }

    public function mapping()
    {
        return ElasticClient::getInstance()->mapping($this->_index, $this->_type);
    }
}
