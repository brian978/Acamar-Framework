<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2015
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Log;

/**
 * Class LogEntry
 *
 * @package Acamar\Log
 */
class LogEntry
{
    /**
     * @var string
     */
    protected $line = "";

    /**
     * @var string
     */
    protected $level = "";

    /**
     * @var \DateTime
     */
    protected $timestamp = null;

    /**
     * Creates the LogEntry object
     *
     * @param string $line
     * @param string $level
     */
    public function __construct($line, $level)
    {
        $this->line = $line;
        $this->level = $level;
        $this->timestamp = new \DateTime();
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "[" . $this->timestamp->format("Y-m-d H:i:s") . "] - [" . $this->level . "] - " . $this->line;
    }
}
