<?php


namespace HttpClient\Core;


class HttpClientConfiguration
{
    public
    $baseUri,
    $defaultHeaders,
    $proxy,
    $requestInfo,
    $userAgent,
    $timeOutInMS,
    $allowRedirection,
    $maxRedirect;
}