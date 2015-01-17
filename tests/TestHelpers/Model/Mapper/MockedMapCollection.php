<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace TestHelpers\Model\Mapper;

use Acamar\Model\Mapper\MapCollection;

class MockedMapCollection extends MapCollection
{
    /**
     * An array representing the data in the collection
     *
     * @var array
     */
    protected $collection = [
        'default' => [
            'entity' => '\TestHelpers\Model\Entity\MockEntity',
            'specs' => [
                'id' => 'id',
                'field1' => 'testField1',
                'joinedId' => [
                    'toProperty' => 'testField2',
                    'map' => 'defaultJoin'
                ],
            ]
        ],
        'defaultArray' => [
            'entity' => '\TestHelpers\Model\Entity\ArrayMockEntity',
            'identField' => 'id',
            'specs' => [
                'id' => 'id',
                'testField1' => 'testField1',
                'testField2' => [
                    'toProperty' => 'testField2',
                ]
            ]
        ],
        'defaultJoin' => [
            'entity' => '\TestHelpers\Model\Entity\MockEntity',
            'specs' => [
                'joinedId' => 'id',
                'joinedField1' => 'testField1',
                'joinedField2' => 'testField2',
            ]
        ],
        'collectionDefault' => [
            'entity' => '\TestHelpers\Model\Entity\CEntity1',
            'identField' => 'id',
            'specs' => [
                'id' => 'id',
                'name' => 'name',
                'childId' => [
                    'toProperty' => 'cEntity2',
                    'map' => 'collectionJoinCEntity2'
                ],
            ]
        ],
        'collectionJoinCEntity2' => [
            'entity' => '\TestHelpers\Model\Entity\CEntity2',
            'identField' => 'childId',
            'specs' => [
                'childId' => 'id',
                'childName' => 'name',
                'childTypeId' => 'typeId',
                'childId2' => [
                    'toProperty' => 'cEntity3',
                    'map' => 'collectionJoinCEntity3'
                ],
            ]
        ],
        'collectionJoinCEntity3' => [
            'entity' => '\TestHelpers\Model\Entity\CEntity3',
            'identField' => 'childId2',
            'specs' => [
                'childId2' => 'id',
                'childName2' => 'name'
            ]
        ],
        'collectionJoinComposedEntity1' => [
            'entity' => '\TestHelpers\Model\Entity\ComposedEntity1',
            'identField' => ['someId1', 'someId2'],
            'specs' => [
                'someId1' => 'id1',
                'someId2' => 'id2',
                'foreignField' => [
                    'toProperty' => 'collectionField',
                    'map' => 'collectionJoinComposedEntity2'
                ]
            ]
        ],
        'collectionJoinComposedEntity2' => [
            'entity' => '\TestHelpers\Model\Entity\ComposedEntity2',
            'identField' => ['fId'],
            'specs' => [
                'fId' => 'id',
                'foreignField' => 'field',
            ]
        ],
        'collectionArrayDefault' => [
            'entity' => '\TestHelpers\Model\Entity\CEntity1',
            'identField' => 'id',
            'specs' => [
                'id' => 'id',
                'name' => 'name',
                'arrValues' => [
                    'toProperty' => 'arrValues',
                ],
                'childId' => [
                    'toProperty' => 'cEntity2',
                    'map' => 'collectionJoinCEntity2'
                ],
            ]
        ],
    ];
}
