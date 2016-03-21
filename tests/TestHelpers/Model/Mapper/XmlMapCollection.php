<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace TestHelpers\Model\Mapper;

use Acamar\Model\Mapper\Collection\MapCollection;

/**
 * Class XmlMapCollection
 *
 * @package TestHelpers\Model\Mapper
 */
class XmlMapCollection extends MapCollection
{
    /**
     * An array representing the data in the collection
     *
     * @var array
     */
    protected $collection = [
        'catalog_item' => [
            'entity' => '\TestHelpers\Model\Entity\CatalogItem',
            'specs' => [
                'item_number' => 'number',
                'size' => array(
                    'toProperty' => 'sizes',
                    'map' => 'item_sizes'
                )
            ]
        ]
    ];
}
