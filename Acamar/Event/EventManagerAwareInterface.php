<?php
/**
 * Acamar-PHP
 *
 * @link https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Event;

/**
 * Interface EventManagerAwareInterface
 *
 * @package Acamar\Event
 */
interface EventManagerAwareInterface
{
    /**
     * Used to inject the EventManager in the object
     *
     * @param EventManager $eventManager
     * @return mixed
     */
    public function setEventManager(EventManager $eventManager);
} 
