<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
