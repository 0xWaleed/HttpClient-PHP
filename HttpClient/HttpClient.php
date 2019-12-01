<?php

namespace HttpClient;

use HttpClient\Core\HttpClientConfiguration;
use HttpClient\Core\HttpRequestOptions;
use HttpClient\Interfaces\HttpDataInterface;
use HttpClient\Core\HttpResponse;
use HttpClient\Interfaces\HttpForwardInterface;

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
    private const DEFAULT_INSTANCE_ID = 'DEFAULT';

    private $_id;

    private $_defaultHeaders = [];
    private $_baseUri = '';
    private $_getRequestInfo;
    private $_userAgent;
    private $_timeOutInMS;
    private $_allowRedirection;
    private $_maxRedirection;

    private static $_instances = [];

    public $beforeRequestDelegate;

    public static $onAllRequestDelegate;

    private function __construct($id)
    {
        $this->_id = $id;
    }

    /**
     * @param string $id
     * @return HttpClient
     */
    public static function instance($id = self::DEFAULT_INSTANCE_ID)
    {
        if (!isset(self::$_instances[$id]))
            self::$_instances[$id] = new static($id);
        return self::$_instances[$id];
    }

    public function config(HttpClientConfiguration $httpClientConfiguration)
    {
        $this->_defaultHeaders = $httpClientConfiguration->defaultHeaders;
        $this->_baseUri = $httpClientConfiguration->baseUri;
        $this->_getRequestInfo = (bool)$httpClientConfiguration->requestInfo;
        $this->_userAgent = $httpClientConfiguration->userAgent;
        $this->_timeOutInMS = $httpClientConfiguration->timeOutInMS;
        $this->_allowRedirection = $httpClientConfiguration->allowRedirection;
        $this->_allowRedirection = $httpClientConfiguration->allowRedirection;
        $this->_maxRedirection = $httpClientConfiguration->maxRedirect;
    }

    public function authorization($type, $token)
    {
        $this->_defaultHeaders['Authorization'] = "{$type} $token";
    }

    private function setUp($method, $url, ?HttpRequestOptions $requestOptions)
    {
        $responseObj = self::createResponseObject();
        $curl = curl_init();

        if (!$requestOptions) {
            $requestOptions = new HttpRequestOptions();
        }

        $allHeaders = [];
        $curlOptions = [];

        if (is_callable(self::$onAllRequestDelegate)) {
            call_user_func(self::$onAllRequestDelegate, $requestOptions, $this->_id);
        }

        if (is_callable($this->beforeRequestDelegate)) {
            call_user_func($this->beforeRequestDelegate, $requestOptions);
        }

        if (!empty($this->_defaultHeaders)) {
            self::mergeHeadersWithDefaultHeaders($allHeaders);
        }

        if (!is_null($this->_allowRedirection)) {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = $this->_allowRedirection;
        }

        $url = self::setFinalUri($url);


        self::assignRequestOptions($requestOptions, $allHeaders, $curlOptions, $url);


        self::buildHeadersForCurl($allHeaders, $curlOptions);

        $curlOptions[CURLOPT_URL] = $url;

        $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;

        $curlOptions[CURLOPT_RETURNTRANSFER] = true;

        $curlOptions[CURLOPT_HEADER] = true;

        $curlOptions[CURLOPT_ENCODING] = '';

        $curlOptions[CURLINFO_HEADER_OUT] = true;

        $curlOptions[CURLOPT_PROTOCOLS] = CURLPROTO_HTTPS | CURLPROTO_HTTP;

        if (($timeOut = $this->_timeOutInMS))
            $curlOptions[CURLOPT_TIMEOUT_MS] = $timeOut;

        $curlOptions[CURLOPT_USERAGENT] = ($userAgent = $this->_userAgent) ? $userAgent : self::DEFAULT_USER_AGENT;

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        $headersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $responseHeaders = substr($response, 0, $headersSize);

        $responseBody = substr($response, $headersSize);

        $responseObj->body = $responseBody;

        $responseObj->headers = $responseHeaders;

        if ($this->_getRequestInfo) {
            $responseObj->request = curl_getinfo($curl);
        }

        curl_close($curl);

        return $responseObj;
    }

    public function get($url, HttpRequestOptions $requestOptions = null)
    {
        return self::setUp('GET', $url, $requestOptions);
    }

    public function post($url, HttpRequestOptions $requestOptions = null)
    {
        return self::setUp('POST', $url, $requestOptions);
    }

    public function put($url, HttpRequestOptions $requestOptions = null)
    {
        return self::setUp('PUT', $url, $requestOptions);
    }

    public function delete($url, HttpRequestOptions $requestOptions = null)
    {
        return self::setUp('DELETE', $url, $requestOptions);
    }

    public function patch($url, HttpRequestOptions $requestOptions = null)
    {
        return self::setUp('PATCH', $url, $requestOptions);
    }

    public function requestFromClass(HttpForwardInterface $httpForward): HttpResponse
    {
        $httpOptions = new HttpRequestOptions();
        $httpOptions->setBody($httpForward->getBody());
        $httpOptions->setCookies($httpForward->getCookies());
        $httpOptions->setHeaders($httpForward->getHeaders());
        return self::setUp($httpForward->getMethod(), $httpForward->getUrl(), $httpOptions);
    }

    private function setFinalUri($url)
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme']))
            $url = $this->_baseUri . $url;

        return $url;
    }

    private static function assignUrlArgs(HttpRequestOptions &$requestOptions, &$url)
    {
        $url .= '?' . http_build_query($requestOptions->getArgs());
    }

    /**
     * @return HttpResponse
     */
    private static function createResponseObject()
    {
        static $responseObj;
        if (!$responseObj)
            $responseObj = new HttpResponse();
        return $responseObj;
    }

    private static function buildHeadersForCurl($allHeaders, &$curlOptions)
    {
        $allHeadersFinal = [];

        array_walk($allHeaders, function ($v, $k) use (&$allHeadersFinal) {
            $allHeadersFinal[] = "{$k}: {$v}";
        });

        $curlOptions[CURLOPT_HTTPHEADER] = $allHeadersFinal;
    }


    private static function mergeHeadersWithRequestedHeaders(?HttpRequestOptions $requestOptions, array &$allHeaders): void
    {
        $allHeaders = array_merge($allHeaders, $requestOptions->getHeaders());
    }

    private static function assignBodyFromRequestOptions(?HttpRequestOptions $requestOptions, array &$allHeaders, array &$curlOptions): void
    {
        $allHeaders['Content-Type'] = $requestOptions->getBody()->contentType();
        $curlOptions[CURLOPT_POSTFIELDS] = $requestOptions->getBody()->body();
    }

    private function mergeHeadersWithDefaultHeaders(array &$allHeaders): void
    {
        $allHeaders = array_merge($allHeaders, $this->_defaultHeaders);
    }


    private static function assignCookiesToCurl(?HttpRequestOptions $requestOptions, array &$curlOptions): void
    {
        $curlOptions[CURLOPT_COOKIE] = http_build_query($requestOptions->getCookies(), null, ';');
    }

    private static function assignRequestOptions(?HttpRequestOptions $requestOptions, &$allHeaders, &$curlOptions, &$url): void
    {
        if (is_array($requestOptions->getHeaders()) && !empty($requestOptions->getHeaders())) {
            self::mergeHeadersWithRequestedHeaders($requestOptions, $allHeaders);
        }

        if (($requestOptions->getBody() instanceof HttpDataInterface)) {
            self::assignBodyFromRequestOptions($requestOptions, $allHeaders, $curlOptions);
        }

        if (!empty($requestOptions->getCookies())) {
            self::assignCookiesToCurl($requestOptions, $curlOptions);
        }

        if (!is_null($requestOptions->getAllowRedirection())) {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = (bool)$requestOptions->getAllowRedirection();
        }

        if (!is_null($requestOptions->getMaxRedirection())) {
            $curlOptions[CURLOPT_MAXREDIRS] = (int)$requestOptions->getMaxRedirection();
        }

        if (!empty($requestOptions->getArgs())) {
            self::assignUrlArgs($requestOptions, $url);
        }

    }
}