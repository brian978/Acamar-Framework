<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc\View\Helper;

use Acamar\Config\Config;
use Acamar\Config\ConfigAwareInterface;
use Acamar\Event\Event;
use Acamar\Event\EventAwareInterface;
use Acamar\Mvc\ApplicationInterface;

/**
 * Class Url
 *
 * @package Acamar\Mvc\View\Helper
 */
class Url implements ConfigAwareInterface, EventAwareInterface, HelperInterface
{
    /**
     * @var Config
     */
    private $config = null;

    /**
     * @var \Acamar\Mvc\Event\MvcEvent
     */
    protected $event = null;

    /**
     * Inject the configuration object
     *
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Used to inject an Event into the object
     *
     * @param Event|\Acamar\Mvc\Event\MvcEvent $event
     * @return $this
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Creates an URL
     *
     * @param string $routeName
     * @param array $params
     * @return string
     * @throws \RuntimeException
     */
    public function __invoke($routeName, $params = array())
    {
        if (!is_string($routeName) || !isset($this->config['routes'][$routeName])) {
            throw new \RuntimeException('Invalid route');
        }

        /** @var $target \Acamar\Mvc\ApplicationInterface */
        $target = $this->event->getTarget();
        if ($target instanceof ApplicationInterface === false) {
            throw new \RuntimeException('The target of the event must be the application');
        }

        return $target->getRouter()->getRoute($routeName)->assemble($params, $this->event->getRoute());
    }
}