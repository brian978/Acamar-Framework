<?php
/**
 * Acamar-PHP
 *
 * @link https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Event;

use Acamar\Event\EventManager;
use PHPUnit_Framework_TestCase;

/**
 * Class EventManagerTest
 *
 * @package AcamarTest\Event
 */
class EventManagerTest extends PHPUnit_Framework_TestCase
{
    public function testEventsCanBeAttachedAndTriggered()
    {
        $someVar      = 0;
        $eventManager = new EventManager();

        $eventManager->attach("test", function () use (&$someVar) {
            $someVar++;
        });

        $eventManager->trigger("test");

        $this->assertEquals(1, $someVar);
    }
} 
