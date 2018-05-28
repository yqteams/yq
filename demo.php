<?php

require_once dirname(__FILE__).'/vendor/autoload.php';

use YQ\YqExtend;
use YQ\Elastic\DemoElastic;
use YQ\Caches\YqWeixinJsapiTicketCache;
use YQ\YqEmail;

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



// YqWeixinJsapiTicketCache::getInstance()->update(1001, [
//     'booth_id' => 1002,
//     'create_time' => time(),
//     'name' => '连州糖水',
//     'trade_id' => 1,
//     'summary' => '好喝下火，香甜可口，过来看看瞧瞧吧111111111111111111',
// ]);


// $ret007 = YqWeixinJsapiTicketCache::getInstance()->get(1001);

// print_r($ret007);



// 存放文件目录
// $path = $putname = YqExtend::uniqid();
// print_r($path);
// print_r($putname);



// $params = "%7B%22userid%22%3A1118277%2C%22body%22%3A%221980%5Cu91d1%5Cu5e01%22%2C%22fee%22%3A%22198%22%2C%22subject%22%3A%221980%5Cu91d1%5Cu5e01%22%2C%22appId%22%3A%22649%22%2C%22trade_sn%22%3A%22i2018020918465010824%22%2C%22orderId%22%3A%22dba05bedb0157554b4fd222f5e5bc96f%22%2C%22status%22%3A%22succ%22%2C%22createTime%22%3A%222018-02-09_18%3A46%3A50%22%2C%22sign%22%3A%22c41d996f26e4f6c98537abcad993962c%22%7D=";
// print_r(urldecode($params));
// $params = json_decode(urldecode($params), true);
// print_r($params);


// YqEmail::test();
YqEmail::test2();
