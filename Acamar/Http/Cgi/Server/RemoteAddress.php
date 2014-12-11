<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Http\Cgi\Server;

use Acamar\Http\Cgi\Request;

/**
 * Class RemoteAddress
 *
 * @package Acamar\Http\Cgi\Server
 */
class RemoteAddress
{
    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var bool
     */
    protected $useProxy = false;

    /**
     * This will contain the resolved IP address
     *
     * @var string
     */
    protected $resolved = null;

    /**
     * Constructs the RemoteAddress helper
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param boolean $useProxy
     *
     * @return RemoteAddress
     */
    public function setUseProxy($useProxy)
    {
        $this->useProxy = $useProxy;

        return $this;
    }

    /**
     * @return Request
     */
    public function getResolved()
    {
        if(null === $this->resolved) {
            if(true === $this->useProxy) {
                $this->resolved = $this->getIpFromProxy();
            }

            if(empty($this->resolved)) {
                $this->resolved = $this->request->getServer('REMOTE_ADDR');
            }
        }

        return $this->resolved;
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
        $proxyHeader = $this->request->getHeaders()->get('X-Forwarded-For');
        if (false === $proxyHeader) {
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
