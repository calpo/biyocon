<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Http;


class Response
{
    private $headers;
    private $status;
    private $body;
    private $screenShot;

    /**
     * @return int HTTP status code
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array HTTP response headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array HTTP response headers
     */
    public function getHeadersAsText()
    {
        $text = '';
        foreach ($this->getHeaders() as $key => $value) {
            $text .= $key;
            if (!empty($value)) {
                $text .= ': ' . $value;
            }
            $text .= "\n";
        }

        return $text;
    }

    /**
     * @return string content body
     */
    public function getBody()
    {
        if ($this->isJson()) {
            return static::formatJson($this->body);
        }
        return $this->body;
    }

    /**
     * @return boolean
     */
    public function isJson()
    {
        $contentType = @$this->getHeaders()['Content-Type' ];
        if (!$contentType) {
            return false;
        }

        return (boolean)preg_match('#application/json#', $contentType);
    }

    /**
     * @return string path to image file
     */
    public function getScreenShot()
    {
        return $this->screenShot;
    }

    /**
     * @param array $headerList
     * @return Response
     */
    public function setHeadersByList(array $headerList)
    {
        foreach ($headerList as $header) {
            list($key, $value) = explode(':', $header);
            $this->headers[trim($key)] = trim($value);
        }

        return $this;
    }

    /**
     * @param array $headers
     * @return Response
     */
    public function setHeadersByHash(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param int $status
     * @return Response
     */
    public function setStatus($status)
    {
        $this->status = (int)$status;
        return $this;
    }

    /**
     * @param string $body
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $screenShot path to image file
     * @return Response
     */
    public function setScreenShot($screenShot)
    {
        $this->screenShot = sys_get_temp_dir() . '/biyocon-response-' . uniqid() . $this->getExtension($screenShot);
        copy($screenShot, $this->screenShot);
        return $this;
    }

    public function __destruct()
    {
        if (file_exists($this->screenShot)) {
            unlink($this->screenShot);
        }
    }

    private function getExtension($file)
    {
        if (!preg_match('/.*(\.[0-9a-zA-Z]+?)$/', $file, $matches)) {
            return '';
        }

        return $matches[1];
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     * @return string Indented version of the original JSON string.
     */
    private static function formatJson($json)
    {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=1; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}
