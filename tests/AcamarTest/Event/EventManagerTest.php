<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Event;

use Acamar\Event\Event;
use Acamar\Event\EventManager;
use PHPUnit\Framework\TestCase;

/**
 * Class EventManagerTest
 *
 * @package AcamarTest\Event
 */
class EventManagerTest extends TestCase
{
    public function testEventsCanBeAttachedAndTriggered()
    {
        $someVar = 0;
        $eventManager = new EventManager();

        $eventManager->attach("test", function () use (&$someVar) {
            $someVar++;
        });

        $eventManager->trigger("test");

        $this->assertEquals(1, $someVar);
    }

    public function testCanForwardEvent()
    {
        $someVar = 0;
        $eventManager = new EventManager();

        $eventManager->attach("test", function (Event $event) use (&$someVar, $eventManager) {
            $someVar++;
            $eventManager->forward($event, "test2");
        });

        $eventManager->attach("test2", function () use (&$someVar) {
            $someVar++;
        });

        $eventManager->trigger("test");

        $this->assertEquals(2, $someVar);
    }

    public function testCanStopEventForwarding()
    {
        $someVar = 0;
        $eventManager = new EventManager();

        $eventManager->attach("test", function (Event $event) use (&$someVar, $eventManager) {
            $someVar++;
            $event->stopPropagation(true);
            $eventManager->forward($event, "test2");
        });

        $eventManager->attach("test2", function () use (&$someVar) {
            $someVar++;
        });

        $eventManager->trigger("test");

        $this->assertEquals(1, $someVar);
    }
}
