<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Mvc\Router;

use Acamar\Event\EventManager;
use Acamar\Http\Request;
use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\Router\Route;
use Acamar\Mvc\Router\Router;


/**
 * Class RouterTest
 *
 * @package AcamarTest\Mvc\Router
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Router::onRoute
     */
    public function testRouterCanTriggerDispatch()
    {
        $eventManager = new EventManager();
        $router = new Router($eventManager);
        $router->addRoute(new Route('app', '/:controller'));

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri('/index');

        $event = new MvcEvent(MvcEvent::EVENT_ROUTE);
        $event->setRequest($request);

        // Attaching the event handler
        $routed = false;
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function () use (&$routed) {
            $routed = true;
        });

        // Triggering our event so we can assert
        $eventManager->trigger($event);

        $this->assertTrue($routed);
    }
}
