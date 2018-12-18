<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

// use YQ\YqExcel;
 use YQ\YqExtend;

// $ret = YqExcel::read('./payssion.xlsx', 'Sheet1');
// $ret = $ret[0];
// for ($i = 1; $i < count($ret); $i++) {
//     $tmp = [];
//     foreach ($ret[$i] as $key => $val) {
//         $k       = $ret[0][$key];
//         $tmp[$k] = $val;
//     }
//     $data[] = $tmp;
// }

// $payssions = [];
// foreach ($data as $val) {
//     $country_name = $val['country_name'];
//     if (!isset($payssions[$country_name])) {
//         $payssions[$country_name] = [
//             'country_name' => $country_name,
//             'country'      => $val['country'],
//             'country_icon' => $val['country_icon'],
//             'pays'         => [],
//         ];
//     }
//     $payssions[$country_name]['pays'][] = [
//         'pay_type'   => $val['pay_type'],
//         'pm_id'      => $val['pm_id'],
//         'need_email' => $val['need_email'],
//     ];
// }

// echo var_export($payssions, true);
// echo json_encode($payssions, JSON_UNESCAPED_UNICODE);

// echo date('G', time());

echo YqExtend::getRandom(9);
