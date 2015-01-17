<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace AcamarTest\Http;

use Acamar\Http\Headers;
use Acamar\Http\Response;


/**
 * Class ResponseTest
 *
 * @package AcamarTest\Http
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Acamar\Http\Response::getContentType
     */
    public function testResponseContentType()
    {
        $response = new Response();
        $response->getHeaders()->set(Headers::CONTENT_TYPE, 'text/json');

        $this->assertEquals('text/json', $response->getContentType());
    }

    /**
     * @covers Acamar\Http\Response::getStatusHeaderString
     */
    public function testResponseStatusHeader()
    {
        $this->assertEquals('HTTP/1.1 200 OK', Response::getStatusHeaderString(200));
    }

    /**
     * To avoid creating the response multiple times
     *
     * @return array
     */
    public function responseProvider()
    {
        return [
            [Response::fromString(file_get_contents(realpath(__DIR__ . '/_files/response_multi_headers.txt')))]
        ];
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     */
    public function testCreateResponseFromString(Response $response)
    {
        $this->assertInstanceOf('\Acamar\Http\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     */
    public function testCreateResponseFromStringWithHeaders(Response $response)
    {
        $this->assertEquals(256, $response->getHeaders()->get(Headers::CONTENT_LENGTH));
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     */
    public function testCreateResponseFromStringWithStatusPhrase(Response $response)
    {
        $this->assertEquals('OK', $response->getStatusPhrase());
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     */
    public function testCreateResponseFromStringWithBody(Response $response)
    {
        $json = json_decode($response->getBody());
        if (!is_object($json)) {
            $json = new \stdClass();
        }

        $this->assertEquals('ok', $json->status);
    }
}
