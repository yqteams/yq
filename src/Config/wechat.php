<?php

return [
    'appid'  => env('WECHAT_APPID', 'wxd526bf81705f40ea'), // AppID
    'secret' => env('WECHAT_SECRET', '4348e285f73ee32666a90a9cb55002bf'), // AppSecret
    'mch_id' => env('WECHAT_MCH_ID', ''), // 商户名称
    'key'    => env('WECHAT_MCH_KEY', ''), // 支付key
    'notify_url'    => env('WECHAT_PAY_NOTIFY_URL', 'http://s.freecao.com/pay/callback/weixin'), // 支付回调
];
