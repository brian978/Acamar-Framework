<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc;

use Acamar\Event\EventManager;
use Acamar\Http\Response;
use Acamar\Mvc\Controller\AbstractController;
use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\View;

class Dispatcher
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     *
     * @param \Acamar\Event\EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->attach(MvcEvent::EVENT_DISPATCH, [$this, 'dispatch']);
    }

    /**
     * The method will extract the route from the event and dispatch it
     *
     * @param MvcEvent $e
     * @throws \RuntimeException
     * @return void
     */
    public function dispatch(MvcEvent $e)
    {
        $dispatched = false;
        $eventName = MvcEvent::EVENT_RENDER;

        $route = $e->getRoute();
        $controllerClass = $route->getControllerClass();
        $actionMethod = $route->getAction();

        if (class_exists($controllerClass)) {
            /** @var $controller \Acamar\Mvc\Controller\AbstractController */
            $controller = new $controllerClass($e);
            if (is_callable([$controller, $actionMethod])) {
                $this->call($controller, $actionMethod);
                $dispatched = true;
            }
        } else {
            throw new \RuntimeException('The `' . $controllerClass . '` controller was not be found');
        }

        if ($dispatched === false) {
            throw new \RuntimeException('Could not dispatch the request');
        }

        $this->eventManager->forward($e, $eventName);
    }

    /**
     * Calls the action from the given controller
     *
     * @param AbstractController $controller
     * @param string $method
     * @return void
     */
    public function call(AbstractController $controller, $method)
    {
        // Running the method in the controller and capturing any potential echoed data
        ob_start();

        $view = null;
        $return = $controller->$method();

        // The return type of the action can be of the following types: Response, View, array, NULL
        if (is_array($return) || $return instanceof View) {
            if (is_array($return)) {
                $view = new View();
                foreach ($return as $key => $value) {
                    $view->set($key, $value);
                }

                $return = $view;
            }

            $controller->getEvent()->setView($return);
        } elseif ($return instanceof Response) {
            // Although an event object is already created in the event, there is a chance we
            // might want to return an response object of our own
            $controller->getEvent()->setResponse($return);
        }

        // Don't care what is printed out in the controller
        ob_end_clean();
    }
}
