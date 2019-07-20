<?php

use HttpClient\Core\HttpClientConfiguration;
use HttpClient\Core\RequestOptions;
use HttpClient\HttpClient;
use \HttpClient\Models\{
  HttpMultipartCell,
  HttpMultipartData
};
//region AutoLoad
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('HttpClient'));
$allFiles = [];

foreach ($files as $file)
{
    if ($file->getFileName()[0] === '.')
        continue;

    $allFiles[$file->getFileName()] = $file->getPathName();
}

spl_autoload_register(function ($class) use ($allFiles){


    $pathAsArray = explode('\\', $class);

    $fileName = end($pathAsArray).'.php';

    if (file_exists($allFiles[$fileName]))
        require_once $allFiles[$fileName];

});
//endregion


$auth = '06b4331d-84f1-4842-b150-7b1e45a9d36b';
$url = 'https://vl.api.np.km.playstation.net/vl/api/v1/mobile/users/me/info';

HttpClient::authorization('Bearer', $auth);

$res = HttpClient::get($url);
$headers = $res->headers()[0]['Content-Type'];

header("Content-Type: {$headers}");

die(($res->body));

