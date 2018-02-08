<?php

/**
 * xls处理
 */
require_once dirname(__DIR__).'/vendor/autoload.php';

use YQ\YqExcel;

$data1 = YqExcel::read('./charge8月.xls');
$data2 = YqExcel::read('./charge10月.xls');

$rows = [];
foreach ($data1[0] as $key => $val) {
    $sdk_uid = $val[0];
    $platform = $val[4];
    $money = $val[6];
    if (!isset($rows[$platform][$sdk_uid])) {
        $rows[$platform][$sdk_uid] = ['money'=>0];
    }
    $rows[$platform][$sdk_uid]['money'] += $money;
}

$ins = [];
foreach ($rows as $platform => $val1) {
    foreach ($val1 as $sdk_uid => $val2) {
        $ins[] = [$platform, "'$sdk_uid'", $val2['money']];
    }
}

$ret = YqExcel::write('./charge.xls', ['1'=>$ins]);
print_r($ret);
