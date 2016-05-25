<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
