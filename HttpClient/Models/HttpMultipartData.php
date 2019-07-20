<?php


namespace HttpClient\Models;


use HttpClient\Interfaces\HttpDataInterface;

class HttpMultipartData implements HttpDataInterface
{

    private
        $_boundary,
        $_cells;

    /**
     * HttpMultipartData constructor.
     * @param HttpMultipartCell[] $cells
     */
    public function __construct(HttpMultipartCell ...$cells)
    {
        $this->_boundary = md5(time().microtime());
        $this->_cells = $cells;
    }


    public function body()
    {
        $body = '';
        $boundary = $this->_boundary;

        foreach ($this->_cells as $cell)
        {

            $elementBody = sprintf("--%s\r\n", $boundary);
            $elementBody .= sprintf("Content-Type: %s\r\n", $cell->mime);
            $elementBody .= sprintf("Content-Disposition: form-data; name=\"%s\";\r\n", $cell->name);

            if ($cell->fileName)
                $elementBody .= sprintf("filename=\"%s\"\r\n", $cell->fileName);

            $elementBody .= sprintf("Content-Length: %u\r\n\r\n", $cell->length);
            $elementBody .= sprintf("%s\r\n", $cell->data);

            $body .= $elementBody;
        }

        $body .= "--{$boundary}--";
        return $body;
    }

    public function contentType()
    {
        return sprintf("multipart/form-data; boundary=%s", $this->_boundary);
    }
}