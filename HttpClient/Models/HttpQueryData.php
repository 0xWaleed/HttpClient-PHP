<?php


namespace HttpClient\Models;


use HttpClient\Interfaces\HttpDataInterface;

class HttpQueryData implements HttpDataInterface
{

    private
        $_data = [];

    /**
     * HttpQueryData constructor.
     * @param array $_data
     */
    public function __construct(array $_data)
    {
        $this->_data = $_data;
    }


    public function body()
    {
        return http_build_query($this->_data);
    }

    public function contentType()
    {
        return 'application/x-www-form-urlencoded';
    }

}