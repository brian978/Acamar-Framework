<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2015
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Log;

use Acamar\Log\LogLevel;

/**
 * Class LogLevelTest
 *
 * @package AcamarTest\Log
 */
class LogLevelTest extends \PHPUnit_Framework_TestCase
{
    public function testLogLevelPriority()
    {
        $this->assertEquals([
                LogLevel::EMERGENCY,
                LogLevel::ALERT,
                LogLevel::CRITICAL,
                LogLevel::ERROR,
                LogLevel::WARNING,
                LogLevel::NOTICE,
                LogLevel::INFO,
                LogLevel::DEBUG
            ],
            LogLevel::getLevels()
        );
    }

    public function testLogLevelMaximumLevel()
    {
        $this->assertEquals([
                LogLevel::WARNING,
                LogLevel::NOTICE,
                LogLevel::INFO,
                LogLevel::DEBUG
            ],
            array_values(LogLevel::getLevels(LogLevel::WARNING))
        );
    }
} 
