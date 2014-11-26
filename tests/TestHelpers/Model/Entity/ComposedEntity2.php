<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace TestHelpers\Model\Entity;


use Acamar\Model\Entity\AbstractEntity;
use Acamar\Model\Entity\EntityCollection;

class ComposedEntity2 extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $someField = '';

    /**
     * @param int $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $someField
     */
    public function setSomeField($someField)
    {
        $this->someField = $someField;
    }

    /**
     * @return string
     */
    public function getSomeField()
    {
        return $this->someField;
    }
}
