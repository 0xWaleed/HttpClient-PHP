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
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
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
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addCookie(string $name, string $value)
    {
        $this->cookies[$name] = $value;
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
     * @return HttpRequestOptions
     */
    public function setArgs(array $args): HttpRequestOptions
    {
        $this->args = $args;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addArg(string $name, string $value)
    {
        $this->args[$name] = $value;
        return $this;
    }


}