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


class MyClassThatCanBeForwarded implements \HttpClient\Interfaces\HttpForwardInterface
{
    public function getHeaders(): array
    {
        return getallheaders();
    }

    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function getUrl(): string
    {
        return 'http://httpbin.org/anything';
    }

    public function getBody(): \HttpClient\Interfaces\HttpDataInterface
    {
        return new \HttpClient\Models\HttpPlainText(file_get_contents('php://input'));
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}


$myClass = new MyClassThatCanBeForwarded();

$res = HttpClient::requestFromClass($myClass);

die(($res->body));