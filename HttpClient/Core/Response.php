<?php


namespace HttpClient\Core;


class Response
{
    public $body;
    public $headers;
    public $request;

    private $cacheHeaders = null;

    public function bodyAsJson()
    {
        return json_decode($this->body);
    }

    public function headers()
    {
        $headers = $this->headers;

        $headersMD5toCache = md5($headers);
        static $allHeaders;

        if ($this->cacheHeaders && $headersMD5toCache == $this->cacheHeaders)
        {
            return $allHeaders;
        }

        $allHeaders = explode("\r\n\r\n", $headers);
        array_pop($allHeaders);
        $allHeadersCount = count($allHeaders);


        for ($currentHeaderIndex = 0; $currentHeaderIndex < $allHeadersCount; $currentHeaderIndex++)
        {
            $explodedHeaderNode = explode("\r\n", $allHeaders[$currentHeaderIndex]);
            $explodedHeaderNodeCount = count($explodedHeaderNode);
            $allParsedHeader = [];
            for ($i = 0; $i < $explodedHeaderNodeCount; $i++)
            {
                if ($i === 0)
                {
                    $allParsedHeader['Status'] = $explodedHeaderNode[$i];
                    continue;
                }

                list($key, $value) = explode(':', $explodedHeaderNode[$i]);

                $allParsedHeader[$key] = trim($value);

            }
            $allHeaders[$currentHeaderIndex] = $allParsedHeader;

        }
        $this->cacheHeaders = $headersMD5toCache;
        return $allHeaders;
    }
}