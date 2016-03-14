<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace TestHelpers\Model\Entity;

use Acamar\Model\Entity\AbstractEntity;
use Acamar\Model\Entity\EntityCollection;

class CEntity1 extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $name = "";

    /**
     * @var array
     */
    protected $arrValues = array();

    /**
     * @var \Acamar\Model\Entity\EntityCollection
     */
    protected $cEntity2 = null;

    /**
     * @param int $id
     *
     * @return CEntity1
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
     * @param string $name
     *
     * @return CEntity1
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $arrValues
     * @return $this
     */
    public function setArrValues($arrValues)
    {
        if (is_array($arrValues)) {
            $this->arrValues = $arrValues;
        } else {
            $this->arrValues[] = $arrValues;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArrValues()
    {
        return $this->arrValues;
    }

    /**
     * @param \Acamar\Model\Entity\EntityCollection $cEntity2
     *
     * @return CEntity1
     */
    public function setCEntity2(EntityCollection $cEntity2)
    {
        $this->cEntity2 = $cEntity2;

        return $this;
    }

    /**
     * @return \Acamar\Model\Entity\EntityCollection
     */
    public function getCEntity2()
    {
        return $this->cEntity2;
    }
}
