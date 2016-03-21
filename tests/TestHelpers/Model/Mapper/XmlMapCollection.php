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
        'default' => [
            'entity' => '\Acamar\Model\Entity\XmlEntity',
            'specs' => [
                'id' => 'id',
                'field1' => 'testField1',
                'joinedId' => [
                    'toProperty' => 'testField2',
                    'map' => 'defaultJoin'
                ],
            ]
        ]
    ];
}
