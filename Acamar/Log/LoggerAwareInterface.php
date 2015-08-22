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
 * Interface LoggerAwareInterface
 *
 * @package Acamar\Log
 */
interface LoggerAwareInterface
{
    /**
     * @param Logger $logger
     * @return null
     */
    public function setLogger(Logger $logger);
} 
