<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Mvc\View;

use Acamar\Config\Config;
use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\ViewHelperManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ViewHelperManagerTest
 *
 * @package AcamarTest\Mvc\View
 */
class ViewHelperManagerTest extends TestCase
{
    /**
     * @covers ViewHelperManager::getHelper
     */
    public function testCanReturnHelper()
    {
        $manager = new ViewHelperManager();
        $manager->setConfig(new Config());
        $manager->setEvent(new MvcEvent());

        $this->assertInstanceOf('\Acamar\Mvc\View\Helper\Url', $manager->getHelper('url'));
    }

    /**
     * @covers ViewHelperManager::getHelper
     */
    public function testCanReturnHelperWithoutDependencies()
    {
        $manager = new ViewHelperManager();

        $this->assertInstanceOf('\Acamar\Mvc\View\Helper\Url', $manager->getHelper('url'));
    }
}
