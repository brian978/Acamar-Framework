<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTest\Http;

use Acamar\Http\Request;

/**
 * Class RequestTest
 *
 * @package AcamarTest\Http
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Request::getHeaders
     */
    public function testReturnsEmptyHeadersObject()
    {
        $this->assertInstanceOf('\Acamar\Http\Headers', (new Request())->getHeaders());
    }

    /**
     * @return array
     */
    public function requestQuerySetterDataProvider()
    {
        return array(
            array('test', new \stdClass(), null),
            array('test2', 'testString', 'testString'),
            array('test3', 12345, '12345')
        );
    }

    /**
     * @dataProvider requestQuerySetterDataProvider
     * @covers Request::setQuery
     */
    public function testQuerySetterAcceptsOnlyStringOrNumber($name, $value, $assertValue)
    {
        $request = new Request();
        $request->setQuery($name, $value);

        $this->assertEquals($assertValue, $request->getQuery($name));
    }

    /**
     * @return array
     */
    public function requestPostSetterDataProvider()
    {
        return array(
            array('test', new \stdClass(), null),
            array('test2', 'testString', 'testString'),
            array('test3', 12345, '12345')
        );
    }

    /**
     * @dataProvider requestQuerySetterDataProvider
     * @covers Request::setPost
     */
    public function testPostSetterAcceptsOnlyStringOrNumber($name, $value, $assertValue)
    {
        $request = new Request();
        $request->setPost($name, $value);

        $this->assertEquals($assertValue, $request->getPost($name));
    }

    public function testSetPostParamsCanHandleArrays()
    {
        $params = [
            'id' => 1,
            'book' => array(
                'bookId' => '10   ',
            )
        ];

        $request = new Request();
        $request->setPostParams($params);

        $this->assertEquals('10', $request->getPost('book')['bookId']);
    }
}
