<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTest\Mvc\View;

use Acamar\Mvc\View\View;


/**
 * Class ViewTest
 *
 * @package AcamarTest\Mvc\View
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid route
     */
    public function testViewCanReturnHelper()
    {
        $view = new View();

        $this->assertInstanceOf('\Acamar\Mvc\View\Helper\Url', $view->url('test'));
    }
}