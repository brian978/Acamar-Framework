<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace TestHelpers\Model\Entity;


use Acamar\Model\Entity\AbstractEntity;
use Acamar\Model\Entity\EntityCollection;

class ComposedEntity1 extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id1 = 0;

    /**
     * @var int
     */
    protected $id2 = 0;

    /**
     * @var EntityCollection
     */
    protected $collectionField = null;

    /**
     * @param \Acamar\Model\Entity\EntityCollection $collectionField
     */
    public function setCollectionField(EntityCollection $collectionField)
    {
        $this->collectionField = $collectionField;
    }

    /**
     * @return \Acamar\Model\Entity\EntityCollection
     */
    public function getCollectionField()
    {
        return $this->collectionField;
    }

    /**
     * @param int $id1
     */
    public function setId1($id1)
    {
        $this->id1 = $id1;
    }

    /**
     * @return int
     */
    public function getId1()
    {
        return $this->id1;
    }

    /**
     * @param int $id2
     */
    public function setId2($id2)
    {
        $this->id2 = $id2;
    }

    /**
     * @return int
     */
    public function getId2()
    {
        return $this->id2;
    }
}
