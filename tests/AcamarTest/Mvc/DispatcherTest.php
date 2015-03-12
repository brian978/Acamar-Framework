<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Mvc;

use Acamar\Event\EventManager;
use Acamar\Http\Response;
use Acamar\Mvc\Dispatcher;
use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\View;

/**
 * Class DispatcherTest
 *
 * @package AcamarTest\Mvc
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;

    /**
     * @var \Acamar\Mvc\Controller\AbstractController|\PHPUnit_Framework_MockObject_MockObject
     */
    public $controller;

    /**
     * Sets up the fixture
     *
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(new EventManager());

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Acamar\Mvc\Controller\AbstractController $mockController */
        $this->controller = $this->getMockBuilder('\Acamar\Mvc\Controller\AbstractController')
            ->disableOriginalConstructor()
            ->setMethods(['getEvent', 'testAction'])
            ->getMock();

        $this->controller->expects($this->any())
            ->method('getEvent')
            ->will($this->returnValue(new MvcEvent()));
    }

    public function testDispatcherControllerCallWithArray()
    {
        $this->controller->expects($this->any())
            ->method('testAction')
            ->will($this->returnValue([
                'test' => 1
            ]));

        $this->dispatcher->call($this->controller, 'testAction');

        $this->assertEquals(1, $this->controller->getEvent()->getView()->get('test'));
    }

    public function testDispatcherControllerCallWithResponseObject()
    {
        $response = new Response();
        $response->setBody('test');

        $this->controller->expects($this->any())
            ->method('testAction')
            ->will($this->returnValue($response));

        $this->dispatcher->call($this->controller, 'testAction');

        $this->assertEmpty($this->controller->getEvent()->getView());
        $this->assertEquals('test', $this->controller->getEvent()->getResponse()->getBody());
    }

    public function testDispatcherControllerCallWithView()
    {
        $view = new View();
        $view->set('test', 1);

        $this->controller->expects($this->any())
            ->method('testAction')
            ->will($this->returnValue($view));

        $this->dispatcher->call($this->controller, 'testAction');

        $this->assertEquals(1, $this->controller->getEvent()->getView()->get('test'));
    }
}
