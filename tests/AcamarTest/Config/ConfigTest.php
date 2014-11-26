<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Config;

use Acamar\Config\Config;
use PHPUnit_Framework_TestCase;

/**
 * Class ConfigTest
 *
 * @package AcamarTest\Config
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected static $configObj;

    protected $initialArray = array(
        'item1' => 1,
        'item2' => 2,
        'item3' => 3
    );

    public static function setUpBeforeClass()
    {
        self::$configObj = new Config();
    }

    public function testAddInitialConfig()
    {

        self::$configObj->add($this->initialArray);

        $this->assertEquals($this->initialArray, self::$configObj->getArrayCopy());
    }

    public function testUpdateInitialArray()
    {
        $new = array(
            'item2' => 10,
            'item4' => 5
        );

        self::$configObj->add($new);

        $this->assertEquals(
            array_merge($this->initialArray, $new),
            self::$configObj->getArrayCopy()
        );
    }

    public function testSimpleValueReplacedByArray()
    {
        $new = array(
            'item4' => array(
                'subitem1' => 1,
                'subitem2' => 2
            )
        );

        self::$configObj->add($new);

        $this->assertEquals(
            array(
                'item1' => 1,
                'item2' => 10,
                'item3' => 3,
                'item4' => array(
                    'subitem1' => 1,
                    'subitem2' => 2
                )
            ),
            self::$configObj->getArrayCopy()
        );
    }

    /**
     * @depends testSimpleValueReplacedByArray
     */
    public function testArrayMergeRecursive()
    {
        $new = array(
            'item4' => array(
                'subitem2' => 5,
                'subitem3' => 1
            )
        );

        self::$configObj->add($new);

        $this->assertEquals(
            array(
                'item1' => 1,
                'item2' => 10,
                'item3' => 3,
                'item4' => array(
                    'subitem1' => 1,
                    'subitem2' => 5,
                    'subitem3' => 1
                )
            ),
            self::$configObj->getArrayCopy()
        );
    }

    public function testRetrieveValueObjectStyle()
    {
        $this->assertEquals(1, self::$configObj->item1);
    }

    public function testOffsetIsset()
    {
        $this->assertTrue(isset(self::$configObj['item1']));
    }

    public function testOffsetIssetObjectStyle()
    {
        $this->assertTrue(isset(self::$configObj->item1));
    }

    public function testCanMergeWithAnotherConfig()
    {
        $config = new Config(array("item1" => 1));
        $config->merge(new Config(array("item2" => 2)));

        $this->assertEquals(array("item1" => 1, "item2" => 2), $config->getArrayCopy());
    }
}
