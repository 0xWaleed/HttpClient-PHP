<?php

namespace HttpClient\Core;

class HttpResponse
{
    public $body;
    public $headers;
    public $request;

    private $cacheHeaders = null;

    /**
     * @return object|null
     */
    public function bodyAsJson()
    {
        return json_decode($this->body);
    }

    /**
     * Return all headers in case if redirection is enabled
     * @return array
     */
    public function allHeaders()
    {
        $headers = $this->headers;
        $headersMD5toCache = md5($headers);
        static $allHeaders;

        if ($this->cacheHeaders && $headersMD5toCache == $this->cacheHeaders) {
            return $allHeaders;
        }

        $allHeaders = explode("\r\n\r\n", $headers);
        array_pop($allHeaders);
        $allHeadersCount = count($allHeaders);


        for ($currentHeaderIndex = 0; $currentHeaderIndex < $allHeadersCount; $currentHeaderIndex++) {
            $explodedHeaderNode = explode("\r\n", $allHeaders[$currentHeaderIndex]);
            $explodedHeaderNodeCount = count($explodedHeaderNode);
            $allParsedHeader = [];
            for ($i = 0; $i < $explodedHeaderNodeCount; $i++) {
                if ($i === 0) {
                    $statusLine = $explodedHeaderNode[$i];
                    $explodedStatusLine = explode(' ', $statusLine);
                    $allParsedHeader = [
                        'http-version' => $explodedStatusLine[0],
                        'status-code' => $explodedStatusLine[1],
                        'status-text' => $explodedStatusLine[2]
                    ];
                    continue;
                }

                $headerLine = $explodedHeaderNode[$i];
                $firstColonPosition = strpos($headerLine, ':');

                if ($firstColonPosition === false)
                    continue;

                $key = substr($headerLine, 0, $firstColonPosition);
                $key = trim(strtolower($key));
                $value = trim(substr($headerLine, $firstColonPosition + 1));

                if ($key === 'set-cookie')
                    $allParsedHeader[$key][] = new Cookie($value);
                else
                    $allParsedHeader[$key] = $value;

            }
            $allHeaders[$currentHeaderIndex] = $allParsedHeader;

        }
        $this->cacheHeaders = $headersMD5toCache;
        return $allHeaders;
    }

    /**
     * Return last header in case if redirection is enabled
     * @return array
     */
    public function lastHeader()
    {
        $headers = $this->allHeaders();
        $lastHeader = end($headers);
        return $lastHeader;
    }

    /**
     * @param string $key
     * @return string|array|null
     */
    public function getHeader(string $key)
    {
        $headers = $this->lastHeader();
        $key = strtolower($key);
        if (isset($headers[$key]))
            return $headers[$key];
        return null;
    }

    /**
     * @return array|null
     */
    public function getCookies()
    {
        return $this->getHeader('set-cookie');
    }

    /**
     * @api this return null if cookie not found, make sure to check the return value before fetching its data.
     * @param string $name
     * @return Cookie|null
     */
    public function getCookie($name)
    {
        $cookies = $this->getCookies();
        foreach ($cookies as $cookie)
        {
            if ($name === $cookie->name)
                return $cookie;
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getContentType()
    {
        return $this->getHeader('Content-Type');
    }
}