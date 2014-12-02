<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Http;

use Countable;
use Iterator;

/**
 * Class Headers
 *
 * @package Acamar\Http
 */
class Headers implements Countable, Iterator
{
    /**
     * Request fields
     *
     * TODO: Add all
     */
    const ACCEPT          = 'Accept';
    const ACCEPT_CHARSET  = 'Accept-Charset';
    const ACCEPT_ENCODING = 'Accept-Encoding';
    const ACCEPT_LANGUAGE = 'Accept-Language';
    const ACCEPT_DATETIME = 'Accept-Datetime';
    const AUTHORIZATION   = 'Authorization';
    const CACHE_CONTROL   = 'Cache-Control';
    const COOKIE          = 'Cookie';
    const CONTENT_LENGTH  = 'Content-Length';
    const CONTENT_MD5     = 'Content-MD5';
    const CONTENT_TYPE    = 'Content-Type';
    const ORIGIN          = 'Origin';
    const USER_AGENT      = 'User-Agent';

    /**
     * Response fields
     *
     * TODO: Add all
     */
    const ETAG = 'ETag';

    /**
     * List of headers
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Creates a Headers object from a string
     *
     * @param string $string
     * @return Headers
     */
    public static function fromString($string)
    {
        $lines = explode("\r\n", $string);
        if (empty($lines) || $lines[0] == $string) {
            $lines = explode("\n", $string);
        }

        return static::fromArray($lines);
    }

    /**
     * Creates a Headers object from an array
     *
     * @param array $lines
     * @return Headers
     */
    public static function fromArray(array &$lines)
    {
        /** @var $headers Headers */
        $headers = new static();

        if(count($lines)) {
            do {
                $line = array_shift($lines);

                // Matching the header name and value
                if (preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):\W+(?P<value>.*)$/', $line, $matches)) {
                    $headers->set($matches['name'], $matches['value']);
                } elseif (preg_match('/^\s*$/', $line)) {
                    // Finished with the headers
                    break;
                }
            } while(false !== $line);
        }

        return $headers;
    }

    /**
     * Sets a header
     *
     * @param string $name
     * @param string $value
     * @param bool $replace If set to true it will replace the "$name" header
     * @return $this
     */
    public function set($name, $value, $replace = true)
    {
        if (!is_string($name) || !is_string($value)) {
            return $this;
        }

        if ($replace || !isset($this->headers[$name])) {
            $this->headers[$name] = $value;
        } elseif (false === $replace) {
            if (!is_array($this->headers[$name])) {
                $this->headers[$name] = array(
                    $this->headers[$name],
                    $value
                );
            } else {
                $this->headers[$name][] = $value;
            }
        }

        return $this;
    }

    /**
     * Returns the requested header (if it exists)
     *
     * @param string $name Name of the header to return. Eg: Content-Type
     * @return bool
     */
    public function get($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return false;
    }

    /**
     * Returns all the headers in array format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->headers;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->headers);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->headers);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->headers);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return (current($this->headers) !== false);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->headers);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->headers);
    }
}
