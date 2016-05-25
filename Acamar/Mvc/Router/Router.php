<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Mvc\Router;

use Acamar\Event\EventManager;
use Acamar\Http\Request;
use Acamar\Mvc\Event\MvcEvent;

/**
 * Class Router
 *
 * @package Acamar\Mvc\Router
 */
class Router
{
    /**
     * @var \Acamar\Event\EventManager
     */
    protected $eventManager = null;

    /**
     * List of routes
     *
     * @var array
     */
    protected $routes = [];

    /**
     * @var string
     */
    protected $routeClass = '\Acamar\Mvc\Router\Route';

    /**
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
    }

    /**
     * Routes the request and triggers the dispatch event
     *
     * @param MvcEvent $e
     * @throws \RuntimeException
     */
    public function onRoute(MvcEvent $e)
    {
        $request = $e->getRequest();
        $route = $this->match($request);

        if ($route === null) {
            throw new \RuntimeException('Invalid request');
        }

        // Adding the route to the event
        $e->setRoute($route);

        // We trigger the already created object so we can control what type of event object it is triggered
        $this->eventManager->forward($e, MvcEvent::EVENT_DISPATCH);
    }

    /**
     * Return route object that match the given HTTP method and URI
     *
     * @param Request $request
     * @return Route
     */
    public function match(Request $request)
    {
        $uri = $request->getUri();

        /** @var $route Route */
        foreach ($this->routes as $route) {
            if ($route->matches($uri) && $route->acceptsHttpRequest($request)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getRouteClass()
    {
        return $this->routeClass;
    }

    /**
     * Adds a new route in the router
     *
     * @param Route $route
     * @return $this
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * Locates a route in the route stack and returns it (if it finds it)
     *
     * @param string $name
     * @return null|Route
     */
    public function getRoute($name)
    {
        /** @var $route \Acamar\Mvc\Router\Route */
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }
}
