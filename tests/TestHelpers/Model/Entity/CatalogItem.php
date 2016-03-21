<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace TestHelpers\Model\Entity;

use Acamar\Model\Entity\EntityCollection;
use Acamar\Model\Entity\XmlEntity;

/**
 * Class CatalogItem
 *
 * @package TestHelpers\Model\Entity
 */
class CatalogItem extends XmlEntity
{
    /**
     * @var string
     */
    protected $number = "";

    /**
     * @var EntityCollection
     */
    protected $sizes = null;

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return EntityCollection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @param EntityCollection $sizes
     */
    public function setSizes(EntityCollection $sizes)
    {
        $this->sizes = $sizes;
    }
}