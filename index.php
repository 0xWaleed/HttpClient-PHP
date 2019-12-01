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

foreach ($files as $file) {
    if ($file->getFileName()[0] === '.')
        continue;

    $allFiles[$file->getFileName()] = $file->getPathName();
}

spl_autoload_register(function ($class) use ($allFiles) {


    $pathAsArray = explode('\\', $class);

    $fileName = end($pathAsArray) . '.php';

    if (file_exists($allFiles[$fileName]))
        require_once $allFiles[$fileName];

});
//endregion

//This gonna work on before all requests
HttpClient::$onAllRequestDelegate = function (HttpRequestOptions $r, $id) {
    $r->addHeader('Id', $id);
    $r->addArg('Id', $id);
    $r->addCookie('Id', $id);
};

//region This is example of one instance - Playstation
$httpForSpecificId_playstation = HttpClient::instance('Playstation');
//This gonna work on before all instance requests
$httpForSpecificId_playstation->beforeRequestDelegate = function (HttpRequestOptions $r) use ($httpForSpecificId_playstation) {
    $httpForSpecificId_playstation->authorization('Bearer', 'token-playstation');
    $r->addCookie('npsso', 'dlksdksldkldfhihiihiii-playstation');
};

$res = $httpForSpecificId_playstation->get('https://httpbin.org/anything');

echo '<pre>';

print_r($res->body);

//endregion

//region This another instance with another Id - Community
$httpForSpecificId_Community = HttpClient::instance('Community');

$httpForSpecificId_Community->beforeRequestDelegate = function (HttpRequestOptions $r) use ($httpForSpecificId_Community) {
    $httpForSpecificId_Community->authorization('Bearer', 'token-community');
    $r->addCookie('npsso', 'dlksdksldkldfhihiihiii-community');
};

$res = $httpForSpecificId_Community->get('https://httpbin.org/anything');

echo "\n";

print_r($res->body);
//endregion

