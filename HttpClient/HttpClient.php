<?php

namespace HttpClient;

use HttpClient\Core\HttpClientConfiguration;
use HttpClient\Core\RequestOptions;
use HttpClient\Interfaces\HttpDataInterface;
use HttpClient\Core\Response;
/*
 * TODO:
 * EncodeURL
 *
 * */

/**
 * Class HttpClient
 * @package HttpClient
 */
class HttpClient
{

    private const DEFAULT_USER_AGENT = 'HttpClient';

    private static $_defaultHeaders = [];
    private static $_baseUri = '';
    private static $_getRequestInfo;
    private static $_userAgent;
    private static $_timeOutInMS;
    private static $_allowRedirection;
    private static $_maxRedirection;

    private function __construct()
    {
    }

    public static function config(HttpClientConfiguration $httpClientConfiguration)
    {
        self::$_defaultHeaders = $httpClientConfiguration->defaultHeaders;
        self::$_baseUri = $httpClientConfiguration->baseUri;
        self::$_getRequestInfo = (bool)$httpClientConfiguration->requestInfo;
        self::$_userAgent = $httpClientConfiguration->userAgent;
        self::$_timeOutInMS = $httpClientConfiguration->timeOutInMS;
        self::$_allowRedirection = $httpClientConfiguration->allowRedirection;
        self::$_allowRedirection = $httpClientConfiguration->allowRedirection;
        self::$_maxRedirection = $httpClientConfiguration->maxRedirect;
    }

    public static function authorization($type, $token)
    {
        self::$_defaultHeaders['Authorization'] = "{$type} $token";
    }

    private static function setUp($method, $url, ?RequestOptions $requestOptions)
    {
        $responseObj = self::createResponseObject();
        $curl = curl_init();


        $allHeaders = [];
        $curlOptions = [];

        if (!empty(self::$_defaultHeaders))
        {
            self::mergeHeadersWithDefaultHeaders($allHeaders);

        }

        if (!is_null(self::$_allowRedirection))
        {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = self::$_allowRedirection;
        }

        if ($requestOptions)
        {
            self::assignRequestOptions($requestOptions, $allHeaders, $curlOptions);
        }

        $url = self::setFinalUri($url);

        self::buildHeadersForCurl($allHeaders, $curlOptions);

        $curlOptions[CURLOPT_URL] = $url;

        $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;

        $curlOptions[CURLOPT_RETURNTRANSFER] = true;

        $curlOptions[CURLOPT_HEADER] = true;

        $curlOptions[CURLOPT_ENCODING] = '';

        $curlOptions[CURLINFO_HEADER_OUT] = true;

        $curlOptions[CURLOPT_PROTOCOLS] = CURLPROTO_HTTPS | CURLPROTO_HTTP;

        if (($timeOut = self::$_timeOutInMS))
            $curlOptions[CURLOPT_TIMEOUT_MS] = $timeOut;

        $curlOptions[CURLOPT_USERAGENT] = ($userAgent = self::$_userAgent) ? $userAgent : self::DEFAULT_USER_AGENT;

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        $headersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $responseHeaders = substr($response, 0, $headersSize);

        $responseBody = substr($response, $headersSize);

        $responseObj->body = $responseBody;

        $responseObj->headers = $responseHeaders;

        if (self::$_getRequestInfo)
        {
            $responseObj->request = curl_getinfo($curl);
        }

        curl_close($curl);

        return $responseObj;
    }

    public static function get($url, RequestOptions $requestOptions = null)
    {
        return self::setUp('GET', $url, $requestOptions);
    }

    public static function post($url, RequestOptions $requestOptions = null)
    {
        return self::setUp('POST', $url, $requestOptions);
    }

    public static function put($url, RequestOptions $requestOptions = null)
    {
        return self::setUp('PUT', $url, $requestOptions);
    }

    public static function delete($url, RequestOptions $requestOptions = null)
    {
        return self::setUp('DELETE', $url, $requestOptions);
    }

    public static function patch($url, RequestOptions $requestOptions = null)
    {
        return self::setUp('PATCH', $url, $requestOptions);
    }

    private static function setFinalUri($url)
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme']))
            $url = self::$_baseUri . $url;

        return $url;
    }

    /**
     * @return Response
     */
    private static function createResponseObject()
    {
        static $responseObj;
        if (!$responseObj)
            $responseObj = new Response();
        return $responseObj;
    }

    private static function buildHeadersForCurl($allHeaders, &$curlOptions)
    {
        $allHeadersFinal = [];

        array_walk($allHeaders, function ($v, $k) use (&$allHeadersFinal)
        {
            $allHeadersFinal[] = "{$k}: {$v}";
        });

        $curlOptions[CURLOPT_HTTPHEADER] = $allHeadersFinal;
    }


    private static function mergeHeadersWithRequestedHeaders(?RequestOptions $requestOptions, array &$allHeaders): void
    {
        $allHeaders = array_merge($allHeaders, $requestOptions->getHeaders());
    }

    private static function assignBodyFromRequestOptions(?RequestOptions $requestOptions, array &$allHeaders, array &$curlOptions): void
    {
        $allHeaders['Content-Type'] = $requestOptions->getBody()->contentType();
        $curlOptions[CURLOPT_POSTFIELDS] = $requestOptions->getBody()->body();
    }

    private static function mergeHeadersWithDefaultHeaders(array &$allHeaders): void
    {
        $allHeaders = array_merge($allHeaders, self::$_defaultHeaders);
    }


    private static function assignCookiesToCurl(?RequestOptions $requestOptions, array &$curlOptions): void
    {
        $curlOptions[CURLOPT_COOKIE] = http_build_query($requestOptions->getCookies(), null, ';');
    }

    private static function assignRequestOptions(?RequestOptions $requestOptions, &$allHeaders, &$curlOptions): void
    {
        if (is_array($requestOptions->getHeaders()) && !empty($requestOptions->getHeaders()))
        {
            self::mergeHeadersWithRequestedHeaders($requestOptions, $allHeaders);
        }

        if (($requestOptions->getBody() instanceof HttpDataInterface))
        {
            self::assignBodyFromRequestOptions($requestOptions, $allHeaders, $curlOptions);
        }

        if (!empty($requestOptions->getCookies()))
        {
            self::assignCookiesToCurl($requestOptions, $curlOptions);
        }

        if (!is_null($requestOptions->getAllowRedirection()))
        {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = (bool)$requestOptions->getAllowRedirection();
        }

        if (!is_null($requestOptions->getMaxRedirection()))
        {
            $curlOptions[CURLOPT_MAXREDIRS] = (int)$requestOptions->getMaxRedirection();
        }
    }
}