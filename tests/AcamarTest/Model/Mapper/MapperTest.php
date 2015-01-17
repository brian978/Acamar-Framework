<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTests\Model\Mapper;

use Acamar\Model\Mapper\ObjectMapper;
use TestHelpers\AbstractTest;
use TestHelpers\Model\Mapper\MockedMapCollection;

class MapperTest extends AbstractTest
{
    public function testCanUseTheMapToLinkObjects()
    {
        $mapper = new ObjectMapper(new MockedMapCollection());
        $data = [
            'id' => 1,
            'field1' => 'test',
            'joinedId' => 2,
            'joinedField1' => 'test_joined',
            'joinedField2' => 'test_joined2'
        ];

        $object = $mapper->populate($data);
        $extracted = $mapper->extract($object);

        $this->assertEquals($data, $extracted);
    }

    public function testCanMapMultipleArrayToObjects()
    {
        $mapper = new ObjectMapper(new MockedMapCollection());
        $data = [
            [
                'id' => 1,
                'name' => 'row 1 table 1',
                'childId' => 1,
                'childTypeId' => 1,
                'childName' => 'row 1 table 2',
                'childId2' => 1,
                'childName2' => 'row 1 table 3',
            ],
            [
                'id' => 1,
                'name' => 'row 1 table 1',
                'childId' => 2,
                'childTypeId' => 1,
                'childName' => 'row 2 table 2',
                'childId2' => 2,
                'childName2' => 'row 2 table 3',
            ],
            [
                'id' => 1,
                'name' => 'row 1 table 1',
                'childId' => 2,
                'childTypeId' => 1,
                'childName' => 'row 2 table 2',
                'childId2' => 3,
                'childName2' => 'row 3 table 3',
            ],
            [
                'id' => 1,
                'name' => 'row 1 table 1',
                'childId' => 3,
                'childTypeId' => 2,
                'childName' => 'row 3 table 2',
                'childId2' => 4,
                'childName2' => 'row 4 table 3',
            ],
            [
                'id' => 2,
                'name' => 'row 2 table 1',
                'childId' => 4,
                'childTypeId' => 1,
                'childName' => 'row 4 table 2',
                'childId2' => 5,
                'childName2' => 'row 5 table 3',
            ],
            [
                'id' => 3,
                'name' => 'row 3 table 1'
            ]
        ];

        // Sorting the input array so we can properly compare with the output
        foreach ($data as &$part) {
            ksort($part);
        }

        $object = $mapper->populateCollection($data, 'collectionDefault');
        $extracted = $mapper->extractCollection($object, 'collectionDefault');

        // Sorting the extracted so it matches the input
        foreach ($extracted as &$part) {
            ksort($part);
        }

        $this->assertEquals($data, $extracted);
    }

    public function testWillIgnoreUnidentifiableData()
    {
        $mapper = new ObjectMapper(new MockedMapCollection());
        $data = [
            [
                'id' => 3,
                'name' => 'row 3 table 1',
                'childId' => null,
                'childTypeId' => null,
                'childName' => null,
                'childId2' => null,
                'childName2' => null,
            ]
        ];

        // Sorting the input array so we can properly compare with the output
        foreach ($data as &$part) {
            ksort($part);
        }

        $object = $mapper->populateCollection($data, 'collectionDefault');
        $extracted = $mapper->extractCollection($object, 'collectionDefault');

        // Sorting the extracted so it matches the input
        foreach ($extracted as &$part) {
            ksort($part);
        }

        $this->assertNotEquals($data, $extracted);
    }

    public function testWillFindObjectByComposedKey()
    {
        $mapper = new ObjectMapper(new MockedMapCollection());
        $data = [
            [
                'someId1' => 1,
                'someId2' => 2,
                'fId' => 1,
                'foreignField' => 'something',
            ],
            [
                'someId1' => 1,
                'someId2' => 3,
                'fId' => 2,
                'foreignField' => 'something2',
            ],
            [
                'someId1' => 1,
                'someId2' => 2,
                'fId' => 5,
                'foreignField' => 'something3',
            ],
        ];

        // Sorting the input array so we can properly compare with the output
        foreach ($data as &$part) {
            ksort($part);
        }

        $object = $mapper->populateCollection($data, 'collectionJoinComposedEntity1');
        $extracted = $mapper->extractCollection($object, 'collectionJoinComposedEntity1');

        // Sorting the extracted so it matches the input
        foreach ($extracted as &$part) {
            ksort($part);
        }

        $this->assertNotEquals($data, $extracted);
    }

    public function testCanMapToArrayProperty()
    {
        $mapper = new ObjectMapper(new MockedMapCollection());
        $data = [
            [
                'id' => 1,
                'testField1' => 2,
                'testField2' => 1,
            ],
            [
                'id' => 1,
                'testField1' => 2,
                'testField2' => 2,
            ],
        ];

        // Sorting the input array so we can properly compare with the output
        foreach ($data as &$part) {
            ksort($part);
        }

        $object = $mapper->populateCollection($data, 'defaultArray');
        $extracted = $mapper->extractCollection($object, 'defaultArray');

        // Sorting the extracted so it matches the input
        foreach ($extracted as &$part) {
            ksort($part);
        }

        $this->assertEquals($data, $extracted);
    }

    public function testCanMapToArrayAndCollectionProperty()
    {
        $mapper = new ObjectMapper(new MockedMapCollection());
        $data = [
            [
                'id' => 1,
                'name' => 'first name',
                'arrValues' => 2,
                'childId' => 1,
                'childName' => 'row 1 col 1',
                'childTypeId' => 0
            ],
            [
                'id' => 1,
                'name' => 'first name',
                'arrValues' => 3,
                'childId' => 2,
                'childName' => 'row 2 col 1',
                'childTypeId' => 0
            ],
            [
                'id' => 1,
                'name' => 'first name',
                'arrValues' => 3,
                'childId' => 3,
                'childName' => 'row 3 col 1',
                'childTypeId' => 0
            ],
            [
                'id' => 2,
                'name' => 'some name',
                'arrValues' => null,
                'childId' => 3,
                'childName' => 'row 3 col 1',
                'childTypeId' => 0
            ],
            [
                'id' => 3,
                'name' => 'some other name',
                'childId' => 4,
                'childName' => 'row 4 col 1',
                'childTypeId' => 0
            ],
        ];

        // Sorting the input array so we can properly compare with the output
        foreach ($data as &$part) {
            ksort($part);
        }

        $object = $mapper->populateCollection($data, 'collectionArrayDefault');
        $extracted = $mapper->extractCollection($object, 'collectionArrayDefault');

        // Sorting the extracted so it matches the input
        foreach ($extracted as &$part) {
            ksort($part);
        }

        $this->assertEquals($data, $extracted);
    }
}
