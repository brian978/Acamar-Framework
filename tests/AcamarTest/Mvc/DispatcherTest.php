<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTest\Mvc;

use Acamar\Event\EventManager;
use Acamar\Mvc\Dispatcher;
use Acamar\Mvc\Event\MvcEvent;

/**
 * Class DispatcherTest
 *
 * @package AcamarTest\Mvc
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testDispatcherControllerCall()
    {
        $dispatcher = new Dispatcher(new EventManager());
        $event      = new MvcEvent();

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Acamar\Mvc\Controller\AbstractController $mockController */
        $mockController = $this->getMockBuilder('\Acamar\Mvc\Controller\AbstractController')
            ->disableOriginalConstructor()
            ->setMethods(['getEvent', 'testAction'])
            ->getMock();

        $mockController->expects($this->any())
            ->method('getEvent')
            ->will($this->returnValue($event));

        $mockController->expects($this->any())
            ->method('testAction')
            ->will($this->returnCallback(array($this, 'mockTestAction')));

        $dispatcher->call($mockController, 'testAction');

        $this->assertEquals(1, $event->getView()->get('test'));
    }

    /**
     * This method is used to simulate the testAction() method from the mocked controller
     *
     * @return array
     */
    public function mockTestAction()
    {
        return [
            'test' => 1
        ];
    }
}
