<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTests\Model\Entity;

use PHPUnit_Framework_TestCase;
use TestHelpers\Model\Entity\MockEntity;

class EntityTest extends PHPUnit_Framework_TestCase
{
    public function testEntityCanOutputArray()
    {
        $mock = new MockEntity();
        $mock->setId(1);

        $this->assertEquals(['id' => 1, 'testField1' => '', 'testField2' => ''], $mock->toArray());
    }
}
