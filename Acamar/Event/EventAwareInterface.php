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
 * Interface EventAwareInterface
 *
 * @package Acamar\Event
 */
interface EventAwareInterface
{
    /**
     * Used to inject an Event into the object
     *
     * @param Event $event
     * @return mixed
     */
    public function setEvent(Event $event);
} 
