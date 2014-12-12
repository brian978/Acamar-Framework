<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Model\Entity;

use Acamar\Collection\AbstractCollection;

class EntityCollection extends AbstractCollection implements EntityCollectionInterface
{
    /**
     * @param EntityInterface $entity
     * @return $this
     */
    public function add(EntityInterface $entity)
    {
        $this->collection[] = $entity;

        return $this;
    }
}
