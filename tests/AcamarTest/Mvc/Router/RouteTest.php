<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
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
    /**
     * @covers Acamar\Mvc\Router\Route::matches
     * @covers Acamar\Mvc\Router\Route::getParams
     */
    public function testCanMatchUrl()
    {
        $route = new Route('test', '/:controller/:action');

        $this->assertTrue($route->matches('/index/some-action'));
        $this->assertEquals(array('controller' => 'index', 'action' => 'some-action'), $route->getParams());
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteMatchesWildcard()
    {
        $route = new Route('test', '/:controller/:action');
        $route->matches('/index/some-action/id/1');

        $this->assertEquals(array('controller' => 'index', 'action' => 'some-action', 'id' => 1), $route->getParams());
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteIgnoresOptionalParameters()
    {
        $route = new Route('test', '/:controller(/:action/:id(/:someParam))', array('action' => 'index'));
        $route->matches('/products/index/1');

        $this->assertEquals(array('controller' => 'products', 'action' => 'index', 'id' => 1), $route->getParams());
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteCapturesOptionalParameters()
    {
        $route = new Route('test', '/:controller(/:action)', array('action' => 'index'));
        $route->matches('/products/list');

        $this->assertEquals(array('controller' => 'products', 'action' => 'list'), $route->getParams());
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteCapturesOptionalParametersEventWithSlashesOutsideOptional()
    {
        $route = new Route('test', '/:controller/(:action)', array('action' => 'index'));
        $route->matches('/products/list');

        $this->assertEquals(array('controller' => 'products', 'action' => 'list'), $route->getParams());
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteCapturesOptionalParametersEventWithSlashesAtEndOfOptional()
    {
        $route = new Route('test', '/:controller/(:action/)', array('action' => 'index'));
        $route->matches('/products/list/');

        $this->assertEquals(array('controller' => 'products', 'action' => 'list'), $route->getParams());
    }

    /**
     * @covers Acamar\Mvc\Router\Route::acceptsHttpMethod
     */
    public function testRouteAcceptsHttpMethod()
    {
        $route = new Route('test', '/:controller(/:action)', array('action' => 'index'));

        $this->assertTrue($route->acceptsHttpMethod('GET'));
    }

    /**
     * @covers Acamar\Mvc\Router\Route::acceptsHttpMethod
     */
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

    /**
     * @covers Acamar\Mvc\Router\Route::getParam
     */
    public function testRouteCanPartiallyMatchMultipleOptionalParams()
    {
        $route = new Route('test', '/:controller(/:action(/:id))', array('action' => 'index'));
        $route->matches('/products/random-action');


        $this->assertEquals('random-action', $route->getParam('action'));
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteCanMatchRouteThatContainsLiterals()
    {
        $route = new Route('test', '/some-module/:controller(/:action)', array('action' => 'index'));

        $this->assertTrue($route->matches('/some-module/products/random-action'));
    }

    /**
     * @covers Acamar\Mvc\Router\Route::matches
     */
    public function testRouteDoesNotMatchRouteThatContainsLiterals()
    {
        $route = new Route('test', '/some-module/:controller(/:action)', array('action' => 'index'));

        $this->assertFalse($route->matches('/another-module/products/random-action'));
    }

    /**
     * @covers Acamar\Mvc\Router\Route::assemble
     */
    public function testCanAssembleSimpleRoute()
    {
        $route = new Route('test', '/some-module/:controller(/:action)', array(
            'controller' => 'index',
            'action' => 'index'
        ));

        $url = $route->assemble(array('controller' => 'products', 'action' => 'list'));

        $this->assertEquals('/some-module/products/list', $url);
    }

    /**
     * @covers Acamar\Mvc\Router\Route::assemble
     */
    public function testCanAssembleSimpleRouteAndIgnoreOptionalParameters()
    {
        $route = new Route('test', '/some-module/:controller(/:action)', array(
            'controller' => 'index',
            'action' => 'index'
        ));

        $url = $route->assemble(array('controller' => 'products'));

        $this->assertEquals('/some-module/products', $url);
    }

    /**
     * @covers Acamar\Mvc\Router\Route::assemble
     */
    public function testCanAssembleComplexRouteAndIgnoreOptionalParameters()
    {
        $route = new Route('test', '/:controller(/:action/:id(/:someParam))/some-literal/:test');

        $url = $route->assemble(array('controller' => 'products', 'action' => 'list', 'id' => 1, 'test' => 'e'));

        $this->assertEquals('/products/list/1/some-literal/e', $url);
    }

    /**
     * @covers Acamar\Mvc\Router\Route::assemble
     */
    public function testCanAssembleComplexRouteIgnoreOptionalParametersAndProperlyAddsLiterals()
    {
        $route = new Route('test', '/:controller(/:action/:id(/:someParam))/some-literal/:test');

        $url = $route->assemble(array('controller' => 'products', 'action' => 'list', 'id' => 1, 'test' => 'e'));

        $this->assertEquals('/products/list/1/some-literal/e', $url);
    }

    /**
     * @covers Acamar\Mvc\Router\Route::assemble
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Missing parameter
     */
    public function testCanAssembleComplexRouteThrowErrorOnMissingParam()
    {
        $route = new Route('test', '/:controller(/:action/:id(/:someParam))/some-literal/:test');

        $route->assemble(array('controller' => 'products', 'action' => 'list', 'test' => 'e'));
    }

    /**
     * @covers Acamar\Mvc\Router\Route::assemble
     */
    public function testCanAssembleComplexRouteWithSlashesInDifferentPositions()
    {
        $route = new Route('test', '/:controller/(:action/:id(:someParam)/)some-literal/:test');

        $url = $route->assemble(array('controller' => 'products', 'action' => 'list', 'id' => 1, 'test' => 'e'));

        $this->assertEquals('/products/list/1/some-literal/e', $url);
        $this->assertTrue($route->matches($url));
    }
}
