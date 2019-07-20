<?php


namespace HttpClient\Core;


class HttpResponse
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
                    $statusLine = $explodedHeaderNode[$i];
                    $explodedStatusLine = explode(' ', $statusLine);
                    $allParsedHeader = [
                        'HttpVersion' => $explodedStatusLine[0],
                        'StatusCode' => $explodedStatusLine[1],
                        'StatusText' => $explodedStatusLine[2]
                    ];
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

    public function lastHeader()
    {
        $headers = $this->headers();
        $lastHeader = end($headers);
        return $lastHeader;
    }
}