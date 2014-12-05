<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Http;

/**
 * Class Response
 *
 * @package Acamar\Http
 */
class Response
{
    /**
     * List of status codes and reasons
     *
     * @var array
     */
    protected static $statusCodePhrases = array(
        // Informational codes
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        // Client error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // Server error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    /**
     * @var string
     */
    protected $httpVersion = '';

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var string
     */
    protected $statusPhrase = '';

    /**
     * @var Headers
     */
    protected $headers = null;

    /**
     * Body of the response
     *
     * @var string
     */
    protected $body = '';

    /**
     * Parses a response string and creates a response object
     *
     * @param string $string
     * @return Response
     */
    public static function fromString($string)
    {
        $lines = explode("\r\n", $string);
        if (empty($lines) || $lines[0] == $string) {
            $lines = explode("\n", $string);
        }

        $httpVersion  = '';
        $statusCode   = 0;
        $statusPhrase = '';
        $headers      = Headers::fromArray($lines);
        $body         = '';

        if (count($lines)) {
            $line = array_shift($lines);
            while (false !== $line) {
                $matched = preg_match(
                    '#HTTP\/(?P<version>1\.[0-9]) (?P<status>[0-9]{3}) (?P<statusPhrase>.*)#',
                    $line,
                    $matches
                );

                // Matching the header name and value
                if ($matched) {
                    $httpVersion  = $matches['version'];
                    $statusCode   = $matches['status'];
                    $statusPhrase = $matches['statusPhrase'];
                } else if (!preg_match('/^\s*$/', $line)) {
                    break;
                }

                // We need to shift here because the line we are matching may be a header
                $line = array_shift($lines);
            }


            // The headers must be retrieved only after the response can be populated
            $headers = Headers::fromArray($lines);

            // Now that we only have the body we can build it
            $body = implode("\r\n", $lines);
        }

        /** @var $instance Response */
        $instance = new static;
        $instance->setHttpVersion($httpVersion);
        $instance->setStatusCode($statusCode);
        $instance->setStatusPhrase($statusPhrase);
        $instance->setHeaders($headers);
        $instance->setBody($body);

        return $instance;
    }

    /**
     * @param string $httpVersion
     *
     * @return Response
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * Sets the status code of the response
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int) $statusCode;

        return $this;
    }

    /**
     * Returns the status code for the response
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusPhrase
     * @return $this
     */
    public function setStatusPhrase($statusPhrase)
    {
        $this->statusPhrase = $statusPhrase;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusPhrase()
    {
        return $this->statusPhrase;
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
     * Returns the response headers
     *
     * @return Headers
     */
    public function getHeaders()
    {
        if (null === $this->headers) {
            $this->headers = new Headers();
        }

        return $this->headers;
    }

    /**
     * Sets the body for the response
     *
     * @param string $body
     * @return $this
     */
    public function setBody(
        $body
    ) {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the content type
     *
     * @return string|null
     */
    public function getContentType()
    {
        return $this->getHeaders()->get(Headers::CONTENT_TYPE);
    }

    /**
     * Checks if the status code if from the informational group
     *
     * @return bool
     */
    public function isInformational()
    {
        $code = $this->statusCode;

        return ($code >= 100 && $code < 200);
    }

    /**
     * Checks if this is a "Not Found" response
     *
     * @return bool
     */
    public function isNotFound()
    {
        return (404 === $this->statusCode);
    }

    /**
     * Checks if the response is OK
     *
     * @return bool
     */
    public function isOk()
    {
        return (200 === $this->statusCode);
    }

    /**
     * Checks if the status code is a server error
     *
     * @return bool
     */
    public function isServerError()
    {
        $code = $this->statusCode;

        return (500 <= $code && 600 > $code);
    }

    /**
     * Checks if response is a redirect
     *
     * @return bool
     */
    public function isRedirect()
    {
        $code = $this->statusCode;

        return (300 <= $code && 400 > $code);
    }

    /**
     * Checks if this is a success status
     *
     * @return bool
     */
    public function isSuccess()
    {
        $code = $this->statusCode;

        return (200 <= $code && 300 > $code);
    }

    /**
     * Returns the status header string for a HTTP response based on the status code
     *
     * @param int $statusCode
     * @return string
     */
    public static function getStatusHeaderString($statusCode)
    {
        $string = 'HTTP/1.1 ' . $statusCode;
        if (isset(static::$statusCodePhrases[$statusCode])) {
            $string .= ' ' . static::$statusCodePhrases[$statusCode];
        }

        return $string;
    }

    /**
     * Returns the phrase for the given status code
     *
     * @param int $statusCode
     * @return string
     */
    public static function getStatusCodePhrase($statusCode)
    {
        if (isset(static::$statusCodePhrases[$statusCode])) {
            return static::$statusCodePhrases[$statusCode];
        }

        return '';
    }

    /**
     * Sends the headers found in the Headers object
     *
     * These should not contain the status header
     *
     * @return void
     */
    protected function sendHeaders()
    {
        $headers = $this->getHeaders()->toArray();
        foreach ($headers as $name => $value) {
            if (strcmp('status', $name) !== 0) {
                header($name . ": " . $value);
            }
        }
    }

    /**
     * Sends the content to the user
     *
     */
    public function sendContent()
    {
        // Setting the headers first (hopefully they were not sent before)
        if (headers_sent() === false) {
            // Sending the status code
            header(static::getStatusHeaderString($this->statusCode));

            // Sending the headers
            $this->sendHeaders();
        }

        echo $this->body;
    }
}
