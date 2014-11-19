<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc;

use Acamar\Event\EventManager;
use Acamar\Http\Response;
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
        $this->eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'dispatch'));
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
        $eventName  = MvcEvent::EVENT_RENDER;

        $route           = $e->getRoute();
        $controllerClass = $route->getControllerClass();
        $actionMethod    = $route->getAction();

        if (class_exists($controllerClass)) {
            /** @var $controller \Acamar\Mvc\Controller\AbstractController */
            $controller = new $controllerClass($e);
            if (is_callable(array($controller, $actionMethod))) {
                // Running the method in the controller and capturing any potential echoed data
                ob_start();

                $view   = null;
                $return = $controller->$actionMethod();

                // The return type of the action can be of the following types: Response, View, array, NULL
                if (!$return instanceof Response) {
                    if (!$return instanceof View) {
                        $view = new View();
                        if (is_array($return)) {
                            foreach ($return as $key => $value) {
                                $view->set($key, $value);
                            }
                        }
                    } else {
                        $view = $return;
                    }

                    $e->setView($view);
                }

                // Don't care what is printed out in the controller
                ob_end_clean();

                $dispatched = true;
            }
        }

        if ($dispatched === false) {
            throw new \RuntimeException('Could not dispatch the request');
        }

        $this->eventManager->forward($e, $eventName);
    }
}
