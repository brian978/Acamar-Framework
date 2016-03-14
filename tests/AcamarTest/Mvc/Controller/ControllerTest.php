<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
     * @covers Acamar\Mvc\Controller\AbstractController::__construct()
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid event target type
     */
    public function testThrowsErrorWhenEventIsWrong()
    {
        $this->getMock('\Acamar\Mvc\Controller\AbstractController', array(), array(new MvcEvent()));
    }
}
