<?php

use HttpClient\Core\HttpClientConfiguration;
use HttpClient\Core\HttpRequestOptions;
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

$reqOptions = new HttpRequestOptions();
$reqOptions->setArgs(['limit' => 10, 'offset' => 0]);
$reqOptions->setBody(new \HttpClient\Models\HttpJsonData(['name' => 'Waleed']));
$r = HttpClient::get('https://httpbin.org/anything', $reqOptions);
echo '<pre>';

die(print_r($r->body));