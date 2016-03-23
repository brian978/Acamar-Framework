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
 * Class CatalogItemSize
 *
 * @package TestHelpers\Model\Entity
 */
class CatalogItemSize extends XmlEntity
{
    /**
     * @var EntityCollection
     */
    protected $colors = null;

    /**
     * @return EntityCollection
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param EntityCollection $colors
     */
    public function setColors(EntityCollection $colors)
    {
        $this->colors = $colors;
    }

}