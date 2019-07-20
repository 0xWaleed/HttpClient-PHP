<?php


namespace HttpClient\Models;


use HttpClient\Interfaces\HttpDataInterface;

class HttpPlainText implements HttpDataInterface
{

    private $_data;
    public function __construct(string $data)
    {
        $this->_data = $data;
    }

    public function body()
    {
        return $this->_data;
    }

    public function contentType()
    {
        return "text/plain";
    }
}