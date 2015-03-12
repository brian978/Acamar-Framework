<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Model\Mapper;

use Acamar\Collection\AbstractCollection;

class MapCollection extends AbstractCollection
{
    /**
     * Tries to locate a map in the collection and returns it
     *
     * @param string $name
     * @return array|null
     */
    public function findMap($name)
    {
        if (isset($this->collection[$name])) {
            return $this->collection[$name];
        }

        return null;
    }

    /**
     * Reverses a map so instead of mapping from "id" to "someId" it will map from "someId" to "id"
     *
     * @param array $map
     * @return array
     */
    public function flip($map)
    {
        $flipped = [];

        if ($map !== null) {
            foreach ($map['specs'] as $fromField => $toField) {
                if (is_string($toField) || is_numeric($toField)) {
                    $flipped[$toField] = $fromField;
                }
            }
        }

        return $flipped;
    }

    /**
     * Returns the identification field for a map (for databases this is usually the primary key)
     *
     * @param string $map The name of the map from which to return the identification field
     * @return string|null
     */
    public function getIdentField($map)
    {
        $map = $this->findMap($map);
        if (null !== $map) {
            return $map['identField'];
        }

        return null;
    }
}
