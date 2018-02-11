<?php
/**
 *上传处理
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use YQ\Upload\UploadBase;

ini_set('display_errors', 1);

$ret = UploadBase::getInstance()->doUpload('file');
exit(json_encode($ret));
