<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTest\Mvc\View;

use Acamar\Config\Config;
use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\ViewHelperManager;

/**
 * Class ViewHelperManagerTest
 *
 * @package AcamarTest\Mvc\View
 */
class ViewHelperManagerTest extends \PHPUnit_Framework_TestCase
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
}
