<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Http;


use Acamar\Http\Headers;

/**
 * Class HeadersTest
 *
 * @package AcamarTest\Http
 */
class HeadersTest extends \PHPUnit_Framework_TestCase
{
    public function testHeadersFromString()
    {
        $headers = Headers::fromString(file_get_contents(realpath(__DIR__ . '/_files/response_headers.txt')));

        $this->assertEquals('Wed, 12 Nov 2014 20:34:29 GMT', $headers->get('Date'));
    }

    public function testCanSetMultipleHeaders()
    {
        $headers = new Headers();
        $headers->set(Headers::ACCEPT_ENCODING, 'utf-8', false);
        $headers->set(Headers::ACCEPT_ENCODING, 'ascii', false);

        $this->assertCount(2, $headers->get(Headers::ACCEPT_ENCODING));
    }

    public function testCanOverwriteHeaders()
    {
        $headers = new Headers();
        $headers->set(Headers::ACCEPT_ENCODING, 'utf-8');
        $headers->set(Headers::ACCEPT_ENCODING, 'ascii');

        $this->assertEquals('ascii', $headers->get(Headers::ACCEPT_ENCODING));
    }
}