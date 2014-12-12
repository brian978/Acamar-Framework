<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace TestHelpers\Model\Entity;

use Acamar\Model\Entity\AbstractEntity;

class MockEntity extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $testField1 = '';

    /**
     * @var string
     */
    protected $testField2 = '';

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $testField1
     * @return MockEntity
     */
    public function setTestField1($testField1)
    {
        $this->testField1 = $testField1;

        return $this;
    }

    /**
     * @return string
     */
    public function getTestField1()
    {
        return $this->testField1;
    }

    /**
     * @param string $testField2
     * @return MockEntity
     */
    public function setTestField2($testField2)
    {
        $this->testField2 = $testField2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTestField2()
    {
        return $this->testField2;
    }
}
