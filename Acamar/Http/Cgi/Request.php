<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Http\Cgi;

use Acamar\Http\Cgi\Server\RemoteAddress;
use Acamar\Http\Headers;
use Acamar\Http\Request as BasicRequest;

/**
 * Class Request
 *
 * @package Acamar\Http\Cgi
 */
class Request extends BasicRequest
{
    /**
     * This contains the array in the $_SERVER variable
     *
     * @var array
     */
    protected $server = [];

    /**
     * @var string
     */
    protected $baseUri = null;

    /**
     * @var \Acamar\Http\Cgi\Server\RemoteAddress
     */
    protected $remoteAddress = null;

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     */
    public function __construct(array $server = null, array $get = null, array $post = null)
    {
        $this->server = (null === $server ? $_SERVER : $server);

        $this->setPathInfo();

        $this->setMethod($this->server['REQUEST_METHOD']);
        $this->setUri($this->server['PATH_INFO']);
        $this->setQueryParams(null === $get ? $_GET : $get);
        $this->setPostParams(null === $post ? $_POST : $post);
    }

    /**
     * @return \Acamar\Http\Headers
     */
    public function getHeaders()
    {
        if (null === $this->headers) {
            $this->headers = Headers::fromServerArray($this->server);
        }

        return $this->headers;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getServer($name, $default = null)
    {
        if (isset($this->server[$name])) {
            return $this->server[$name];
        }


        return $default;
    }

    /**
     * Checks if the `PATH_INFO` is set and tried to detect it if not
     *
     * DO NOT call this before having the `QUERY_STRING` set
     *
     * @throws \RuntimeException
     * @return $this
     */
    protected function setPathInfo()
    {
        if (!isset($this->server['PATH_INFO'])) {
            if (!isset($this->server['REQUEST_URI'])) {
                throw new \RuntimeException('Cannot detect the path info due to lack of information');
            }

            $requestUri = $this->server['REQUEST_URI'];
            $scriptPath = preg_replace('#(\/[\w-]+)\.php#', '', $this->server['SCRIPT_NAME']);

            // Removing the query string from the request URI as well as parts of the script name
            $requestPath = explode('?', $requestUri)[0];
            if (!empty($scriptPath)) {
                $requestPath = preg_replace('#.*' . $scriptPath . '#', '', $requestPath);
            }

            // Updating the PATH_INFO with the proper information (ensuring right slash)
            $this->server['PATH_INFO'] = '/' . trim($requestPath, '/');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        if (null === $this->baseUri) {
            $requestUri = explode('?', $this->server['REQUEST_URI'])[0];
            $this->baseUri = substr($requestUri, 0, strrpos($requestUri, $this->uri));
        }

        return $this->baseUri;
    }

    /**
     * Returns the remote address object that the Request object will use to provide the IP address
     *
     * @return \Acamar\Http\Cgi\Server\RemoteAddress
     */
    public function getRemoteAddress()
    {
        if (null === $this->remoteAddress) {
            $this->remoteAddress = new RemoteAddress($this);
        }

        return $this->remoteAddress;
    }

    /**
     * Returns the IP from where the request originated
     *
     * @return string
     */
    public function getIp()
    {
        return $this->getRemoteAddress()->getResolved();
    }
}
