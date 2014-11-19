<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Mvc\Controller;

use Acamar\Mvc\Event\MvcEvent;


/**
 * Class ControllerTest
 *
 * @package AcamarTest\Mvc\Controller
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The startup event was modified
     */
    public function testThrowsErrorWhenEventIsWrong()
    {
        $this->getMock('\Acamar\Mvc\Controller\AbstractController', [], [new MvcEvent()]);
    }
}
