<?php

namespace YQ\Elastic;

use Elasticsearch\ClientBuilder;

class ElasticClient
{
    /**
     * 连接服务器信息
     * @var string
     */
    protected $host = '127.0.0.1:9200';

    /**
     * 索引(库名)
     * @var string
     */
    protected $index = 'booth';

    /**
     * 类型(表名)
     * @var string
     */
    protected $type = 'booth_info';

    /**
     * 每个字段的属性
     * @var array
     */
    protected $mappingProperties = [
        // 摊id 精确值搜索
        'booth_id' => [
            'type'  => 'integer',
        ],
        // 创建时间
        'create_time' => [
            'type'  => 'integer',
        ],
        // 摊名称 采用ik中文分词
        'name' => [
            'type'     => 'text',
            'analyzer' => 'ik_max_word',
        ],
        // 摊行业类型
        'trade_id' => [
            'type'  => 'integer',
        ],
        // 摊简介
        'summary' => [
            'type'     => 'text',
            'analyzer' => 'ik_max_word',
        ],
    ];

    /**
     * 实例化对象
     * @var Elasticsearch\Client
     */
    protected $client;

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
        $host = $this->host;
        $builder = ClientBuilder::create();
        $builder->setHosts([$host]);
        $this->client = $builder->build();

        // 校验能否ping通服务器
        if (!$this->client->ping()) {
            throw new \Exception("ping elastic server=>[{$host}] error");
        }

        // 校验索引是否已经创建了，如果没有则创建
        if (!$this->exists($this->index)) {
            $this->createIndex();
        }
    }

    /**
     * 创建索引
     * @return bool
     */
    protected function createIndex()
    {
        $params = [
            // 索引名
            'index' => $this->index,
            'body' => [
                // 设置主分片和副本数量
                'settings' => [
                    'number_of_shards'   => 5,
                    'number_of_replicas' => 1,
                ],
                'mappings' => [
                    // 类型
                    $this->type => [
                        // 是否存储源数据
                        '_source' => [
                            'enabled' => true,
                        ],
                        // 遇到新字段返回异常
                        'dynamic' => 'strict',

                        // 设置每个字段类型
                        'properties' => $this->mappingProperties,
                    ]
                ]
            ]
        ];

        $this->client->indices()->create($params);

        return true;
    }

    /**
     * 判断索引是否存在
     * @param  string $index 索引
     * @return boolean
     */
    public function exists()
    {
        return $this->client->indices()->exists(['index'=>$this->index]);
    }

    /**
     * 获取索引的mapping
     * @return array
     */
    public function mapping()
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
        ];
        return $this->client->indices()->getMapping($params);
    }

    /**
     * 删除索引
     * @return boolean
     */
    public function delIndex()
    {
        if (!$this->exists()) {
            return true;
        }

        $this->client->indices()->delete(['index'=>$this->index]);
        return true;
    }

    /**
     * 添加数据
     * @param  string|integer $id   文档唯一id
     * @param  array          $data 内容
     * @return boolean
     */
    public function setDocument($id, array $data)
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $id,
            'body'  => $data
        ];
        return $this->client->index($params);
    }

    /**
     * 删除文档
     * @param  string|integedr $id 文档唯一id
     * @return boolean
     */
    public function delDocument($id)
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $id
        ];
        return $this->client->delete($params);
    }

    /**
     * 表达式全文搜索，此接口建议只用于匹配查询和多词查询，也不建议用户进行过多复杂操作，
     * 尽量采用最简单方式进行处理
     * https://www.elastic.co/guide/cn/elasticsearch/guide/current/match-query.html
     * https://www.elastic.co/guide/cn/elasticsearch/guide/current/match-multi-word.html
     * @param  array  $query query部分检索内容
     * @param  int    $from  从哪个位置开始获取 默认是从位置0开始，最多偏移1000，详情查看文档：
     *                       https://www.elastic.co/guide/cn/elasticsearch/guide/current/pagination.html
     * @param  int    $size  检索多少条数据
     * @return array
     */
    public function search(array $query, int $from=0, int $size=10)
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => $query,
                'from'  => $from,
                'size'  => $size
            ],
        ];
        return $this->client->search($params);
    }

    public function searchMatch(array $match, int $from=0, int $size=10)
    {
        if (count($match)===1) {
            $params = [
                'match' => $match
            ];
        } else {
            $params = [
                'bool' => [
                    'must' => []
                ],
            ];
            foreach ($match as $key => $value) {
                $params['bool']['must'][] = [
                    'match' => [$key=>$value]
                ];
            }
        }
        return $this->search($params, $from, $size);
    }
}
