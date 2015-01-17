<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace TestHelpers\Model\Entity;

class ArrayMockEntity extends MockEntity
{
    /**
     * @var string
     */
    protected $testField2 = [];

    /**
     * @param string $testField2
     * @return MockEntity
     */
    public function setTestField2($testField2)
    {
        $this->testField2[] = $testField2;

        return $this;
    }
}
