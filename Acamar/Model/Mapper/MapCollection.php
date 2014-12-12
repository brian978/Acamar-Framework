<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Model\Mapper;

use Acamar\Collection\AbstractCollection;

class MapCollection extends AbstractCollection
{
    /**
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
     * @param array $map
     * @return array
     */
    public function flip($map)
    {
        $flipped = array();

        if ($map !== null) {
            foreach ($map['specs'] as $fromField => $toField) {
                if (is_string($toField) || is_numeric($toField)) {
                    $flipped[$toField] = $fromField;
                }
            }
        }

        return $flipped;
    }
}
