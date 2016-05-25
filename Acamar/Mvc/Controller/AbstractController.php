<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Mvc\Controller;

use Acamar\Http\Request;
use Acamar\Http\Response;
use Acamar\Mvc\Application;
use Acamar\Mvc\Event\MvcEvent;

/**
 * Class AbstractController
 *
 * @package Acamar\Mvc\Controller
 */
abstract class AbstractController
{
    /**
     * Contains the main event that was triggered by the application
     *
     * @var \Acamar\Mvc\Event\MvcEvent
     */
    protected $event = null;

    /**
     * The configuration object of the application
     *
     * @var \Acamar\Config\Config
     */
    protected $config = null;

    /**
     * @param MvcEvent $event
     * @throws \RuntimeException
     */
    public function __construct(MvcEvent $event)
    {
        if ($event->getTarget() instanceof Application === false) {
            throw new \RuntimeException('Invalid event target type');
        }

        $this->event = $event;
        $this->request = $event->getRequest();
        $this->config = $event->getTarget()->getConfig();
    }

    /**
     * Returns the event object that was triggered by the Application object
     *
     * @return MvcEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Returns the request object that was initially passed to the controller
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->event->getRequest();
    }

    /**
     * Returns the response object that the controller will populate
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->event->getResponse();
    }
}
