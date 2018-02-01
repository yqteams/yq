<?php

require_once dirname(__FILE__).'/vendor/autoload.php';

use YQ\YqCurl;
use YQ\Elastic\DemoElastic;
use YQ\Caches\YqWeixinJsapiTicketCache;

// $client = DemoElastic::getInstance();

// $ret003 = $client->mapping();

// $ret004 = $client->setDocument(1002, [
//     'booth_id' => 1002,
//     'create_time' => time(),
//     'name' => '连州糖水',
//     'trade_id' => 1,
//     'summary' => '好喝下火，香甜可口，过来看看瞧瞧吧',
// ]);

// $ret005 = $client->getDocument(1002);

// $ret006 = $client->updateDocument(1002, [
//     'summary' => '都是骗人的',
// ]);

// print_r($ret005);



YqWeixinJsapiTicketCache::getInstance()->update(1001, [
    'booth_id' => 1002,
    'create_time' => time(),
    'name' => '连州糖水',
    'trade_id' => 1,
    'summary' => '好喝下火，香甜可口，过来看看瞧瞧吧111111111111111111',
]);


$ret007 = YqWeixinJsapiTicketCache::getInstance()->get(1001);

print_r($ret007);
