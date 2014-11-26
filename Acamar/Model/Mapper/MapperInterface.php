<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Model\Mapper;

use Acamar\Model\Entity\EntityInterface;

interface MapperInterface
{
    /**
     * @param string $className
     * @return \Acamar\Model\Entity\AbstractEntity
     */
    public function createEntityObject($className);

    /**
     * The method converts and array of data (or an object that can be iterated as an array)
     * to a set of objects
     *
     * @param mixed $data
     * @return mixed
     */
    public function populate($data);

    /**
     * The method does the opposite of the populate method
     *
     * @param \Acamar\Model\Entity\EntityInterface $object
     * @param mixed $map Can be either a string or a Map object
     * @return array
     */
    public function extract(EntityInterface $object, $map = null);
}
