<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
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
    public function testResponseContentType()
    {
        $response = new Response();
        $response->getHeaders()->set(Headers::CONTENT_TYPE, 'text/json');

        $this->assertEquals('text/json', $response->getContentType());
    }

    public function testResponseStatusHeader()
    {
        $this->assertEquals('HTTP/1.1 200 OK', Response::getStatusHeaderString(200));
    }
}
