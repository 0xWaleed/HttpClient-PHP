<?php

use HttpClient\Core\HttpClientConfiguration;
use HttpClient\Core\HttpRequestOptions;
use HttpClient\HttpClient;
use \HttpClient\Models\{
    HttpMultipartCell,
    HttpMultipartData
};
require_once 'vendor/autoload.php';

function operationId()
{
    static $id;
    if (!$id)
        $id = bin2hex(openssl_random_pseudo_bytes(24));
    return $id;
}
//This gonna work on before all requests
HttpClient::$onAllRequestDelegate = function (HttpRequestOptions $r, $id) {
    $r->addHeader('Id', $id);
    $r->addArg('Id', $id);
    $r->addCookie('Id', $id);
    $r->addHeader('Operation-Id', operationId());
    $r->addArg('Operation-Id', operationId());
    $r->addCookie('Operation-Id', operationId());
};

//region This is example of one instance - Playstation
$httpForSpecificId_playstation = HttpClient::instance('Playstation');
//This gonna work on before all instance requests
$httpForSpecificId_playstation->beforeRequestDelegate = function (HttpRequestOptions $r) use ($httpForSpecificId_playstation) {
    $httpForSpecificId_playstation->authorization('Bearer', 'token-playstation');
    $r->addCookie('npsso', 'dlksdksldkldfhihiihiii-playstation');
};

$reqOptions = new HttpRequestOptions();
$reqOptions->setBody(new \HttpClient\Models\HttpJsonData(['name' => 'php']));
$res = $httpForSpecificId_playstation->get('https://httpbin.org/anything', $reqOptions);

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

