<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Event;

use SplPriorityQueue;

/**
 * Class EventManager
 *
 * @package Acamar\Event
 */
class EventManager
{
    /**
     * @var array
     */
    protected $events = array();

    /**
     * @var string
     */
    protected $eventClass = 'Acamar\Event\Event';

    /**
     * @var Event
     */
    protected $eventPrototype = null;

    /**
     * @var Event
     */
    protected $lastEvent = null;

    /**
     * Sets an event class
     *
     * @param string $eventClass
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setEventClass($eventClass)
    {
        if (!class_exists($eventClass)) {
            throw new \InvalidArgumentException('The class ' . $eventClass . ' does not exist');
        }

        $this->eventClass = $eventClass;

        return $this;
    }

    /**
     * Adds an event callback to the queue
     *
     * @param string $event
     * @param \Closure|callable $callback
     * @param int $priority
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function attach($event, $callback, $priority = 1)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("The callback is invalid");
        }

        if (!isset($this->events[$event])) {
            $this->events[$event] = new SplPriorityQueue();
        }

        $this->events[$event]->insert($callback, $priority);

        return $this;
    }

    /**
     * Triggers an event
     *
     * @param string|Event $event
     * @param null|string|object $target
     * @param array $params
     */
    public function trigger($event, $target = null, $params = array())
    {
        if ($event instanceof Event) {
            $e     = $event;
            $event = $e->getName();
        } else {
            $e = $this->getEventPrototype();
            $e->setName($event);
            $e->setTarget($target);
            $e->setParams($params);
        }

        /** @var $queue null|SplPriorityQueue */
        $queue = null;
        if (isset($this->events[$event])) {
            $queue = $this->events[$event];
        }

        // Calling the listeners
        if ($queue !== null) {
            $this->lastEvent = $e;
            foreach ($queue as $callback) {
                call_user_func($callback, $e);
                if ($e->isPropagationStopped()) {
                    break;
                }
            }
        }
    }

    /**
     * Forwards an event / Changes the name of the passed event and triggers it again
     *
     * @param Event $object
     * @param string $event
     */
    public function forward(Event $object, $event)
    {
        if (!$object->isPropagationStopped()) {
            $object->setName($event);
            $this->trigger($object);
        }
    }

    /**
     * Returns the prototype for the event class
     *
     * @return Event
     */
    protected function getEventPrototype()
    {
        if ($this->eventPrototype === null) {
            $this->eventPrototype = new $this->eventClass;
        }

        return $this->eventPrototype;
    }

    /**
     * @return \Acamar\Event\Event
     */
    public function getLastEvent()
    {
        return $this->lastEvent;
    }
}
