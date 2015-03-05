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
     * @return string content body
     */
    public function getBody()
    {
        return $this->body;
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
}
