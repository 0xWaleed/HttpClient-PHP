<?php


namespace HttpClient\Models;


class HttpMultipartCell
{
    public $name;
    public $data;
    public $mime;
    public $length;
    public $fileName;

    /**
     * HttpMultipartCell constructor.
     * @param $name
     * @param $data
     * @param $mime
     * @param $length
     * @param $fileName
     */
    public function __construct($name, $data, $mime, $length, $fileName = null)
    {
        $this->name = $name;
        $this->data = $data;
        $this->mime = $mime;
        $this->length = $length;
        $this->fileName = $fileName;
    }


}