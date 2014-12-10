<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
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
     * @var Headers
     */
    protected $headers = null;

    /**
     * @var array
     */
    protected $queryParams = [];

    /**
     * @var array
     */
    protected $postParams = [];

    /**
     * @param array $server
     * @param array $post
     */
    public function __construct(array $server = null, array $post = null)
    {
        $this->setServer($server);
        $this->setQueryStringAndParseIt();
        $this->setPathInfo(); // Must be called AFTER setQueryStringAndParseIt()
    }

    /**
     * Sets the server array
     *
     * @param array|null $server
     * @return $this
     */
    public function setServer(array $server = null)
    {
        $this->server = (null === $server ? $_SERVER : $server);

        return $this;
    }

    /**
     * @param \Acamar\Http\Headers $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
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
     * Sets a value as a query parameter
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setQuery($name, $value)
    {
        if (is_string($name) && (is_string($value) || is_numeric($value))) {
            $this->queryParams[$name] = (string) $value;
        }

        return $this;
    }

    /**
     * Returns a value from the query parameters based on $name
     *
     * @param string $name
     * @param string $default
     * @return array
     */
    public function getQuery($name, $default = null)
    {
        if (isset($this->queryParams[$name])) {
            return $this->queryParams[$name];
        }

        return $default;
    }

    /**
     * Sets the post array
     *
     * @param array|null $post
     * @return $this
     */
    public function setPost(array $post = null)
    {
        $this->postParams = (null === $post ? $_POST : $post);

        return $this;
    }

    /**
     * Returns a value from the post parameters based on $name
     *
     * @param string $name
     * @param string $default
     * @return array
     */
    public function getPost($name, $default = null)
    {
        if (isset($this->postParams[$name])) {
            return $this->postParams[$name];
        }

        return $default;
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
            $scriptName = str_replace('/index.php', '', $this->server['SCRIPT_NAME']);

            // Removing the query string from the request URI
            $requestUri = str_replace('?' . $this->server['QUERY_STRING'], '', $requestUri);
            $requestUri = preg_replace('#.*' . $scriptName . '#', '', $requestUri);

            // Updating the PATH_INFO with the proper information (ensuring right slash)
            $this->server['PATH_INFO'] = '/' . rtrim($requestUri, '/');
        }

        return $this;
    }

    /**
     * Detects the query string if it's not present and sets it, then it parses it
     *
     * @throws \RuntimeException
     * @return $this
     */
    protected function setQueryStringAndParseIt()
    {
        if (empty($this->server['QUERY_STRING'])) {
            if (!isset($this->server['REQUEST_URI'])) {
                throw new \RuntimeException('Cannot detect the path info due to lack of information');
            }

            if (strpos($this->server['REQUEST_URI'], '?')) {
                $requestUri = $this->server['REQUEST_URI'];

                // If "nginx" is used then it may be configured wrong and we may not have the `QUERY_STRING`
                $queryString = $this->server['QUERY_STRING'];
                if (($argsPos = strpos($requestUri, '?')) !== false && empty($queryString)) {
                    $queryString = substr($requestUri, $argsPos + 1);
                }

                $this->server['QUERY_STRING'] = $queryString;
            }
        }

        parse_str($this->server['QUERY_STRING'], $this->queryParams);

        return $this;
    }

    /**
     * Returns the IP from where the request originated
     *
     * @return string
     */
    public function getIp()
    {
        $ip = $this->getIpFromProxy();
        if (!empty($ip)) {
            return $ip;
        }

        if (isset($this->server['REMOTE_ADDR'])) {
            return $this->server['REMOTE_ADDR'];
        }

        return '';
    }

    /**
     * Retrieves the IP, of the client, that is behind the proxy
     *
     * The header for the proxy should look like this:
     * X-Forwarded-For: client, proxy1, proxy2
     *
     * @return string
     * @see http://en.wikipedia.org/wiki/X-Forwarded-For
     */
    protected function getIpFromProxy()
    {
        $proxyHeader = $this->headers->get('X-Forwarded-For');
        if (!$proxyHeader) {
            return '';
        }

        $ips = explode(',', $proxyHeader);

        // Theoretically the client IP is the first, but since this can be spoofed, it doesn't really matter
        // which one we choose
        $ip = array_shift($ips);
        if (is_string($ip)) {
            $ip = trim($ip);
        }

        // The $ip may be "" or NULL
        if (!empty($ip)) {
            return $ip;
        }

        return '';
    }
}
