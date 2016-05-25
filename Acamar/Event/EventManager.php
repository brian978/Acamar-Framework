<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
    protected $events = [];

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
     * Sets the event prototype that will be used when triggering an event
     *
     * @param \Acamar\Event\Event $eventPrototype
     * @return $this
     */
    public function setEventPrototype(Event $eventPrototype)
    {
        $this->eventPrototype = $eventPrototype;

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
    public function trigger($event, $target = null, $params = [])
    {
        if ($event instanceof Event) {
            $e = $event;
            $event = $e->getName();
        } else {
            $e = $this->createEvent();
            $e->setName($event);
            $e->setTarget($target);
            $e->setParams($params);
        }

        /** @var $queue null|SplPriorityQueue */
        $queue = null;
        if (isset($this->events[$event])) {
            $queue = clone $this->events[$event];
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
     * Forwards an event by converting it to another event (basically it changes the name of the event) and triggers
     * it again (if the propagation is not stopped)
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
     * Creates an event object based on the $eventPrototype object
     *
     * @return Event
     */
    public function createEvent()
    {
        if ($this->eventPrototype === null) {
            $this->eventPrototype = new $this->eventClass;
        }

        return clone $this->eventPrototype;
    }

    /**
     * @return \Acamar\Event\Event
     */
    public function getLastEvent()
    {
        return $this->lastEvent;
    }
}
