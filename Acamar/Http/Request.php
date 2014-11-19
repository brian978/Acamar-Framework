<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Http;

/**
 * Class Request
 *
 * @package Acamar\Http
 */
class Request
{
    const METHOD_HEAD    = 'HEAD';
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * This contains the array in the $_SERVER variable
     *
     * @var array
     */
    protected $server = array();

    /**
     * Constructor for the request object
     *
     */
    public function __construct()
    {
        $this->server = $_SERVER;
    }


    /**
     * Returns the HTTP request method (identified by `REQUEST_METHOD`)
     *
     * @return string
     */
    public function getMethod()
    {
        if (!isset($this->server['REQUEST_METHOD'])) {
            return '';
        }

        return $this->server['REQUEST_METHOD'];
    }

    /**
     * This is the URL that was used for the HTTP request (identified by `PATH_INFO`)
     *
     * @return string
     */
    public function getRequestUri()
    {
        if (!isset($this->server['PATH_INFO'])) {
            $this->detectPathInfo();
        }

        return $this->server['PATH_INFO'];
    }

    /**
     * Returns the query string in array format
     *
     * @return array
     */
    public function getQuery()
    {
        if (empty($this->server['QUERY_STRING']) && strpos($this->server['REQUEST_URI'], '?')) {
            $this->detectQueryString();
        }

        // TODO: optimize this
        parse_str($this->server['QUERY_STRING'], $output);

        return $output;
    }

    /**
     * Detects the request path
     *
     * @return $this
     * @throws \RuntimeException
     */
    protected function detectPathInfo()
    {
        if (!isset($this->server['REQUEST_URI'])) {
            throw new \RuntimeException('Cannot detect the path info due to lack of information');
        }

        $requestUri = $this->server['REQUEST_URI'];
        $scriptName = str_replace('/index.php', '', $this->server['SCRIPT_NAME']);

        if (empty($this->server['QUERY_STRING']) && strpos($this->server['REQUEST_URI'], '?')) {
            $this->detectQueryString();
        }

        // Removing the query string from the request URI
        $requestUri = str_replace('?' . $this->server['QUERY_STRING'], '', $requestUri);
        $requestUri = preg_replace('#.*' . $scriptName . '#', '', $requestUri);

        // Updating the PATH_INFO with the proper information (ensuring right slash)
        $this->server['PATH_INFO'] = '/' . rtrim($requestUri, '/');

        return $this;
    }

    /**
     * Detects the query string
     *
     * @return $this
     */
    protected function detectQueryString()
    {
        $requestUri = $this->server['REQUEST_URI'];

        // If "nginx" is used then it may be configured wrong and we may not have the `QUERY_STRING`
        $queryString = $this->server['QUERY_STRING'];
        if (($argsPos = strpos($requestUri, '?')) !== false && empty($queryString)) {
            $queryString = substr($requestUri, $argsPos + 1);
        }

        $this->server['QUERY_STRING'] = $queryString;

        return $this;
    }
}
