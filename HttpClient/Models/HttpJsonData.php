<?php


namespace HttpClient\Models;


use HttpClient\Interfaces\HttpDataInterface;

class HttpJsonData implements HttpDataInterface
{
    private $_data;

    /**
     * HttpJsonData constructor.
     * @param $_data
     */
    public function __construct($_data)
    {
        $this->_data = $_data;
    }


    public function body()
    {
        return json_encode($this->_data);
    }

    public function contentType()
    {
        return 'application/json';
    }
}