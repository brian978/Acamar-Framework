<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTest\Http;


use Acamar\Http\Headers;
use PHPUnit\Framework\TestCase;

/**
 * Class HeadersTest
 *
 * @package AcamarTest\Http
 */
class HeadersTest extends TestCase
{
    /**
     * @covers Acamar\Http\Headers::fromString()
     */
    public function testHeadersFromString()
    {
        $headers = Headers::fromString(file_get_contents(realpath(__DIR__ . '/_files/response_headers.txt')));

        $this->assertEquals('Wed, 12 Nov 2014 20:34:29 GMT', $headers->get('Date'));
    }

    /**
     * @covers Acamar\Http\Headers::set()
     * @covers Acamar\Http\Headers::get()
     */
    public function testCanSetMultipleHeaders()
    {
        $headers = new Headers();
        $headers->set(Headers::ACCEPT_ENCODING, 'utf-8', false);
        $headers->set(Headers::ACCEPT_ENCODING, 'ascii', false);

        $this->assertCount(2, $headers->get(Headers::ACCEPT_ENCODING));
    }

    /**
     * @covers Acamar\Http\Headers::set()
     * @covers Acamar\Http\Headers::get()
     */
    public function testCanOverwriteHeaders()
    {
        $headers = new Headers();
        $headers->set(Headers::ACCEPT_ENCODING, 'utf-8');
        $headers->set(Headers::ACCEPT_ENCODING, 'ascii');

        $this->assertEquals('ascii', $headers->get(Headers::ACCEPT_ENCODING));
    }

    /**
     * Parses a set of request headers from an emulated $_SERVER array
     *
     */
    public function canParseRequestHeaders()
    {
        $headers = Headers::fromServerArray(require realpath(__DIR__ . '/_files/request_headers.php'));

        return [
            [$headers]
        ];
    }

    /**
     * @covers       Acamar\Http\Headers::fromServerArray
     * @dataProvider canParseRequestHeaders
     */
    public function testParseOnlyValidHeaders(Headers $headers)
    {
        $this->assertEquals(10, $headers->count());
    }

    /**
     * @covers       Acamar\Http\Headers::fromServerArray
     * @dataProvider canParseRequestHeaders
     */
    public function testParseRequestHeadersProperly(Headers $headers)
    {
        $this->assertEquals('gzip, deflate, sdch', $headers->get(Headers::ACCEPT_ENCODING));
    }
}
