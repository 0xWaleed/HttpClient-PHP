<?php


namespace HttpClient\Core;


use HttpClient\Interfaces\HttpDataInterface;

class RequestOptions
{
    private $body;
    private $headers = [];
    private $cookies = [];
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
     * @return RequestOptions
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
     * @return RequestOptions
     */
    public function setHeaders(array $headers): RequestOptions
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
     * @return RequestOptions
     */
    public function setCookies(array $cookies): RequestOptions
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
     * @return RequestOptions
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
     * @return RequestOptions
     */
    public function setMaxRedirection($maxRedirection)
    {
        $this->maxRedirection = (int)$maxRedirection;
        return $this;
    }


}