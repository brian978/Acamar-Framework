<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc\Event;

use Acamar\Event\Event;
use Acamar\Http\Request;
use Acamar\Http\Response;
use Acamar\Mvc\Router\Route;
use Acamar\Mvc\View\View;

/**
 * Class MvcEvent
 *
 * @package Acamar\Mvc\MvcEvent
 */
class MvcEvent extends Event
{
    const EVENT_BOOTSTRAP      = "bootstrap";
    const EVENT_ROUTE          = "route";
    const EVENT_DISPATCH       = "dispatch";
    const EVENT_DISPATCH_ERROR = "dispatch.error";
    const EVENT_RENDER         = "render";
    const EVENT_RENDERED       = "rendered";

    /**
     * Overritten only for the type hint
     *
     * @return \Acamar\Mvc\Application
     */
    public function getTarget()
    {
        return parent::getTarget();
    }

    /**
     * @param \Exception $error
     * @return $this
     */
    public function setError(\Exception $error)
    {
        $this->setParam('__error__', $error);

        return $this;
    }

    /**
     * @return \Exception|null
     */
    public function getError()
    {
        return $this->getParam('__error__');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);

        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->getParam('request');
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->setParam('response', $response);

        return $this;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->getParam('response');
    }

    /**
     * @param Route $route
     * @return $this
     */
    public function setRoute(Route $route)
    {
        $this->setParam('route', $route);

        return $this;
    }

    /**
     * @return Route|null
     */
    public function getRoute()
    {
        return $this->getParam('route');
    }

    /**
     * @param View $view
     * @return $this
     */
    public function setView(View $view)
    {
        $this->setParam('view', $view);

        return $this;
    }

    /**
     * @return View|null
     */
    public function getView()
    {
        return $this->getParam('view');
    }
}
