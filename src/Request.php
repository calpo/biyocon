<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon;

class Request
{
    private $scheme = Scheme::HTTP;
    private $host;
    private $port = 80;
    private $user;
    private $pass;
    private $path = '/';
    private $data;
    private $fragment;

    /**
     * set url
     *
     * @param string $url
     * @return Request
     * @throws \InvalidArgumentException
     */
    public function setUrl($url)
    {
        $parsedUrl = parse_url($url);
        if (empty($parsedUrl)) {
            throw new \InvalidArgumentException("url [$url] is invalid");
        }

        if (!empty($parsedUrl['scheme'])) {
            $this->setScheme($parsedUrl['scheme']);
        }
        if (!empty($parsedUrl['host'])) {
            $this->setHost($parsedUrl['host']);
        }
        if (!empty($parsedUrl['port'])) {
            $this->setPort($parsedUrl['port']);
        }
        if (!empty($parsedUrl['user'])) {
            $this->setUser($parsedUrl['user']);
        }
        if (!empty($parsedUrl['pass'])) {
            $this->setPass($parsedUrl['pass']);
        }
        if (!empty($parsedUrl['path'])) {
            $this->setPath($parsedUrl['path']);
        }
        if (!empty($parsedUrl['query'])) {
            $this->setQuery($parsedUrl['query']);
        }
        if (!empty($parsedUrl['fragment'])) {
            $this->setFragment($parsedUrl['fragment']);
        }

        return $this;
    }

    /**
     * @see Scheme
     * @param string $scheme
     * @return Request
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * set domain
     * ex) example.com
     *
     * @param string $host
     * @return Request
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param int $port
     * @return Request
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * set user of basic auth
     *
     * @param string $user
     * @return Request
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * set password of basic auth
     *
     * @param string $pass
     * @return Request
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * set query string
     *
     * ex) foo=123&bar=abc
     *
     * @param string $query
     * @throws \InvalidArgumentException
     * @return Request
     */
    public function setQuery($query)
    {
        parse_str($query, $data);
        if (!is_array($data)) {
            throw new \InvalidArgumentException("query [$query] is invalid");
        }
        $this->setData($data);
        return $this;
    }

    /**
     * set request parameter as array
     *
     * @param array $data
     * @return Request
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * set URL path
     *
     * ex) /foo/bar.html
     *
     * @param string $path URL path
     * @return Request
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * set fragment
     *
     * http://example.com/hoge#this_is_fragment
     *                         ^^^^^^^^^^^^^^^^
     * @param string $fragment
     * @return Request
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }
}
