<?php


namespace HttpClient\Core;


use HttpClient\Interfaces\HttpDataInterface;

class HttpRequestOptions
{
    private $body;
    private $headers = [];
    private $cookies = [];
    private $args = [];
    private $allowRedirection;
    private $maxRedirection;

    /**
     * @return HttpDataInterface
     */
    public function getBody() : ?HttpDataInterface
    {
        return $this->body;
    }

    /**
     * @param HttpDataInterface $body
     * @return HttpRequestOptions
     */
    public function setBody(HttpDataInterface $body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return HttpRequestOptions
     */
    public function setHeaders(array $headers): HttpRequestOptions
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     * @return HttpRequestOptions
     */
    public function setCookies(array $cookies): HttpRequestOptions
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @return bool|NULL
     */
    public function getAllowRedirection()
    {
        return $this->allowRedirection;
    }

    /**
     * @param bool $allowRedirection
     * @return HttpRequestOptions
     */
    public function setAllowRedirection($allowRedirection)
    {
        $this->allowRedirection = (bool)$allowRedirection;
        return $this;
    }

    /**
     * @return int|NULL
     */
    public function getMaxRedirection()
    {
        return $this->maxRedirection;
    }

    /**
     * @param int $maxRedirection
     * @return HttpRequestOptions
     */
    public function setMaxRedirection($maxRedirection)
    {
        $this->maxRedirection = (int)$maxRedirection;
        return $this;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }


}