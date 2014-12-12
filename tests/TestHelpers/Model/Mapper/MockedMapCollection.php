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
    protected $collection = array(
        'default' => array(
            'entity' => '\TestHelpers\Model\Entity\MockEntity',
            'specs' => array(
                'id' => 'id',
                'field1' => 'testField1',
                'joinedId' => array(
                    'toProperty' => 'testField2',
                    'map' => 'defaultJoin'
                ),
            )
        ),
        'defaultArray' => array(
            'entity' => '\TestHelpers\Model\Entity\ArrayMockEntity',
            'identField' => 'id',
            'specs' => array(
                'id' => 'id',
                'testField1' => 'testField1',
                'testField2' => array(
                    'toProperty' => 'testField2',
                )
            )
        ),
        'defaultJoin' => array(
            'entity' => '\TestHelpers\Model\Entity\MockEntity',
            'specs' => array(
                'joinedId' => 'id',
                'joinedField1' => 'testField1',
                'joinedField2' => 'testField2',
            )
        ),
        'collectionDefault' => array(
            'entity' => '\TestHelpers\Model\Entity\CEntity1',
            'identField' => 'id',
            'specs' => array(
                'id' => 'id',
                'name' => 'name',
                'childId' => array(
                    'toProperty' => 'cEntity2',
                    'map' => 'collectionJoinCEntity2'
                ),
            )
        ),
        'collectionJoinCEntity2' => array(
            'entity' => '\TestHelpers\Model\Entity\CEntity2',
            'identField' => 'childId',
            'specs' => array(
                'childId' => 'id',
                'childName' => 'name',
                'childTypeId' => 'typeId',
                'childId2' => array(
                    'toProperty' => 'cEntity3',
                    'map' => 'collectionJoinCEntity3'
                ),
            )
        ),
        'collectionJoinCEntity3' => array(
            'entity' => '\TestHelpers\Model\Entity\CEntity3',
            'identField' => 'childId2',
            'specs' => array(
                'childId2' => 'id',
                'childName2' => 'name'
            )
        ),
        'collectionJoinComposedEntity1' => array(
            'entity' => '\TestHelpers\Model\Entity\ComposedEntity1',
            'identField' => array('someId1', 'someId2'),
            'specs' => array(
                'someId1' => 'id1',
                'someId2' => 'id2',
                'foreignField' => array(
                    'toProperty' => 'collectionField',
                    'map' => 'collectionJoinComposedEntity2'
                )
            )
        ),
        'collectionJoinComposedEntity2' => array(
            'entity' => '\TestHelpers\Model\Entity\ComposedEntity2',
            'identField' => array('fId'),
            'specs' => array(
                'fId' => 'id',
                'foreignField' => 'field',
            )
        ),
        'collectionArrayDefault' => array(
            'entity' => '\TestHelpers\Model\Entity\CEntity1',
            'identField' => 'id',
            'specs' => array(
                'id' => 'id',
                'name' => 'name',
                'arrValues' => array(
                    'toProperty' => 'arrValues',
                ),
                'childId' => array(
                    'toProperty' => 'cEntity2',
                    'map' => 'collectionJoinCEntity2'
                ),
            )
        ),
    );
}
