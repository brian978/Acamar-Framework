<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
        return array(
            array(Response::fromString(file_get_contents(realpath(__DIR__ . '/_files/response_multi_headers.txt'))))
        );
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     * @param Response $response
     */
    public function testCreateResponseFromString(Response $response)
    {
        $this->assertInstanceOf('\Acamar\Http\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     * @param Response $response
     */
    public function testCreateResponseFromStringWithHeaders(Response $response)
    {
        $this->assertEquals(256, $response->getHeaders()->get(Headers::CONTENT_LENGTH));
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     * @param Response $response
     */
    public function testCreateResponseFromStringWithStatusPhrase(Response $response)
    {
        $this->assertEquals('OK', $response->getStatusPhrase());
    }

    /**
     * @covers       Acamar\Http\Response::fromString
     * @dataProvider responseProvider
     * @param Response $response
     */
    public function testCreateResponseFromStringWithBody(Response $response)
    {
        $json = json_decode($response->getBody());
        if (!is_object($json)) {
            $json = new \stdClass();
        }

        $this->assertEquals('ok', $json->status);
    }

    /**
     * @covers Acamar\Http\Response::fromString
     */
    public function testCreateResponseFromStringWithRLineEndings()
    {
        $response = Response::fromString(
            file_get_contents(realpath(__DIR__ . '/_files/response_headers_r_endings.txt'))
        );

        $this->assertEquals(35, $response->getHeaders()->get(Headers::CONTENT_LENGTH));
    }

    /**
     * @covers Acamar\Http\Response::fromString
     */
    public function testCreateResponseFromStringWithNLineEndings()
    {
        $response = Response::fromString(
            file_get_contents(realpath(__DIR__ . '/_files/response_headers_n_endings.txt'))
        );

        $this->assertEquals(35, $response->getHeaders()->get(Headers::CONTENT_LENGTH));
    }

    /**
     * @covers Acamar\Http\Response::fromString
     */
    public function testCreateResponseFromStringWithRNLineEndings()
    {
        $response = Response::fromString(
            file_get_contents(realpath(__DIR__ . '/_files/response_headers_rn_endings.txt'))
        );

        $this->assertEquals(35, $response->getHeaders()->get(Headers::CONTENT_LENGTH));
    }

    /**
     * @covers Acamar\Http\Response::fromString
     */
    public function testCreateResponseFromImage()
    {
        $ch = curl_init("https://www.google.ro/images/nav_logo242.png");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = Response::fromString(curl_exec($ch));

        curl_close($ch);

        $this->assertNotEmpty($response->getBody());
    }

    /**
     * @covers Acamar\Http\Response::fromString
     */
    public function testCreateResponseFromImageWithoutHeaders()
    {
        $ch = curl_init("https://www.google.ro/images/nav_logo242.png");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = Response::fromString(curl_exec($ch));

        curl_close($ch);

        $this->assertNotEmpty($response->getBody());
    }
}
