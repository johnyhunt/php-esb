<?php

require __DIR__ . '/vendor/autoload.php';

$dsn = 'middleware/asdasd/';
$res = ! ! preg_match('/([A-Za-z1-9]+(\/[A-Za-z1-9]+)?)+/', $dsn);

$target = '/middleware/v1/boodmo/sap';
$basePath = '/middleware';

$path = substr($target, strlen($basePath));
$dsn  = implode(':', ['POST', 'http', $path]);
$q=1;