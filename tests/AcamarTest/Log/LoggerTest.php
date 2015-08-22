<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2015
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Log;

use Acamar\Log\Logger;

/**
 * Class LoggerTest
 *
 * @package AcamarTest\Log
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Acamar\Log\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid adapter alias provided
     */
    public function testInvalidAdapterAlias()
    {
        new Logger("someAdapter");
    }

    /**
     * @expectedException \Acamar\Log\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid adapter provided
     */
    public function testInvalidAdapter()
    {
        new Logger(new \stdClass());
    }

    public function testLoggerCanUseStdOutAdapter()
    {
        $logger = new Logger("stdout");

        ob_start();
        $logger->debug("Debug message");
        $output = ob_get_clean();

        $this->assertEquals(true, (false !== strpos($output, "[debug] - Debug message")));
    }
} 
