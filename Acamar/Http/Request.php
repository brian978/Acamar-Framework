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
        return $this->server['PATH_INFO'];
    }
}
