<?php

require_once dirname(__DIR__).'/yq/vendor/autoload.php';

use YQ\YqCurl;
use YQ\Elastic\ElasticClient;

$client = ElasticClient::getInstance();

$ret003 = $client->mapping();

$ret004 = $client->setDocument(1002, [
    'booth_id' => 1002,
    'create_time' => time(),
    'name' => '连州糖水',
    'trade_id' => 1,
    'summary' => '好喝下火，香甜可口，过来看看瞧瞧吧',
]);

$ret005 = $client->searchMatch([
    'summary' => '火',
    'name' => '连',
]);

print_r($ret005);

