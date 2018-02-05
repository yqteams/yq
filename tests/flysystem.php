<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$adapter = new Local('/');
$filesystem = new Filesystem($adapter);

$file = $_FILES['file'];

$contents = $filesystem->getMimetype($file['tmp_name']);

print_r($contents);
print_r($file);
