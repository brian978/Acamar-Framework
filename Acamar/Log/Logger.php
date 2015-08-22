<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2015
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Log;

use Acamar\Log\Adapter\LogAdapterInterface;
use Acamar\Log\Exception\InvalidArgumentException;

/**
 * Class Logger
 *
 * @package Acamar\Log
 */
class Logger implements LoggerInterface
{
    /**
     * @var LogAdapterInterface
     */
    protected $adapter = null;

    /**
     * @var string
     */
    protected $logEntryClass = "\\Acamar\\Log\\LogEntry";

    /**
     * @var array
     */
    protected static $adapterAlias = [
        "blackhole" => "Acamar\\Log\\Adapter\\Blackhole",
        "stdout" => "Acamar\\Log\\Adapter\\StdOut",
    ];

    /**
     * Creates the Logger object
     *
     * @param LogAdapterInterface|string $adapter
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($adapter)
    {
        if (is_string($adapter)) {
            $adapter = static::createAdapterFromString($adapter);
        }

        if (!$adapter instanceof LogAdapterInterface) {
            throw new InvalidArgumentException("Invalid adapter provided");
        }

        $this->setAdapter($adapter);
    }

    /**
     * @param string $alias
     * @return LogAdapterInterface
     * @throws Exception\InvalidArgumentException
     */
    protected static function createAdapterFromString($alias)
    {
        if (!isset(static::$adapterAlias[$alias])) {
            throw new InvalidArgumentException("Invalid adapter alias provided");
        }

        return new static::$adapterAlias[$alias];
    }

    /**
     * @param \Acamar\Log\Adapter\LogAdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(LogAdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @param string $logEntryClass
     * @return $this
     */
    public function setLogEntryClass($logEntryClass)
    {
        $this->logEntryClass = (string)$logEntryClass;

        return $this;
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace["{" . $key . "}"] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $message = $this->interpolate($message, $context);

        $this->adapter->add(new $this->logEntryClass($message, $level));
    }
}
