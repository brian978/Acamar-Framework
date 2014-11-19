<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Mvc\Router;

use Acamar\Mvc\Router\Route;


/**
 * Class RouteTest
 *
 * @package AcamarTest\Mvc\Router
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testCanMatchUrl()
    {
        $route = new Route('test', '/:controller/:action');

        $this->assertTrue($route->matches('/index/some-action'));
        $this->assertEquals(array('controller' => 'index', 'action' => 'some-action'), $route->getParams());
    }

    public function testRouteMatchesWildcard()
    {
        $route = new Route('test', '/:controller/:action');
        $route->matches('/index/some-action/id/1');

        $this->assertEquals(array('controller' => 'index', 'action' => 'some-action', 'id' => 1), $route->getParams());
    }

    public function testRouteIgnoresOptionalParameters()
    {
        $route = new Route('test', '/:controller(/:action)', array('action' => 'index'));
        $route->matches('/products');

        $this->assertEquals(array('controller' => 'products', 'action' => 'index'), $route->getParams());
    }

    public function testRouteCapturesOptionalParameters()
    {
        $route = new Route('test', '/:controller(/:action)', array('action' => 'index'));
        $route->matches('/products/list');

        $this->assertEquals(array('controller' => 'products', 'action' => 'list'), $route->getParams());
    }

    public function testRouteAcceptsHttpMethod()
    {
        $route = new Route('test', '/:controller(/:action)', array('action' => 'index'));

        $this->assertTrue($route->acceptsHttpMethod('GET'));
    }

    public function testRouteRefusesHttpMethod()
    {
        $options = array(
            'acceptedHttpMethods' => array(
                'POST',
            )
        );

        $route = new Route('test', '/:controller(/:action)', array('action' => 'index'), $options);

        $this->assertFalse($route->acceptsHttpMethod('GET'));
    }
} 
