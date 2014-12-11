<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Mvc\View\Helper;

use Acamar\Config\Config;
use Acamar\Event\EventManager;
use Acamar\Http\Cgi\Request;
use Acamar\Mvc\Router\Route;
use Acamar\Mvc\Router\Router;
use Acamar\Mvc\View\Helper\Url;

/**
 * Class UrlTest
 *
 * @package AcamarTest\Mvc\View\Helper
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Acamar\Mvc\Event\MvcEvent|\PHPUnit_Framework_MockObject_MockObject
     */
    private $event;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        // The application contains a config object
        $config = new Config();
        $config->add(array(
            'routes' => array(
                'mvc' => array(
                    'pattern' => '/:controller(/:action)',
                    'defaults' => array(
                        'module' => 'Application'
                    )
                )
            )
        ));

        // The application contains a router
        $router = new Router(new EventManager());
        $router->addRoute(Route::factory('mvc', $config['routes']['mvc']));

        // The application is required by the event
        $application = $this->getMock('\Acamar\Mvc\ApplicationInterface');

        $application->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($config));

        $application->expects($this->any())
            ->method('getRouter')
            ->will($this->returnValue($router));

        // Mocking the event
        $this->event = $this->getMock('Acamar\Mvc\Event\MvcEvent');

        $this->event->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue($application));
    }

    /**
     * @covers \Acamar\Mvc\View\Helper\Url::__invoke
     */
    public function testCanGenerateUrl()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '/folder/BasePath/index.php',
            'REQUEST_URI' => '/folder/BasePath/products/index?test=1',
            'QUERY_STRING' => 'test=1',
            'REMOTE_ADDR' => '127.0.0.1'
        ));

        $this->event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        // Creating our object
        $urlHelper = new Url();
        $urlHelper->setConfig($this->event->getTarget()->getConfig());
        $urlHelper->setEvent($this->event);

        $url = $urlHelper('mvc', ['controller' => 'products', 'action' => 'add']);

        $this->assertEquals('/folder/BasePath/products/add', $url);
    }

    /**
     * @covers \Acamar\Mvc\View\Helper\Url::__invoke
     */
    public function testCanGenerateUrlWithNoBasePath()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/products/index?test=1',
            'QUERY_STRING' => 'test=1',
            'REMOTE_ADDR' => '127.0.0.1'
        ));

        $this->event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        // Creating our object
        $urlHelper = new Url();
        $urlHelper->setConfig($this->event->getTarget()->getConfig());
        $urlHelper->setEvent($this->event);

        $url = $urlHelper('mvc', ['controller' => 'products', 'action' => 'add']);

        $this->assertEquals('/products/add', $url);
    }

    /**
     * @covers \Acamar\Mvc\View\Helper\Url::__invoke
     */
    public function testCanGenerateUrlFromRoot()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/?test=1',
            'QUERY_STRING' => 'test=1',
            'REMOTE_ADDR' => '127.0.0.1'
        ));

        $this->event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        // Creating our object
        $urlHelper = new Url();
        $urlHelper->setConfig($this->event->getTarget()->getConfig());
        $urlHelper->setEvent($this->event);

        $url = $urlHelper('mvc', ['controller' => 'products', 'action' => 'add']);

        $this->assertEquals('/products/add', $url);
    }
} 
