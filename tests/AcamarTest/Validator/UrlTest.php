<?php
/**
 * Acamar Framework
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Validator;

use PHPUnit_Framework_TestCase;
use Acamar\Validator\Url;

/**
 * Class UrlTest
 *
 * @package AcamarTest\Validator
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Acamar\Validator\Url
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Url();
    }

    public function testIsUrlValidWithoutCUrl()
    {
        $this->assertTrue($this->validator->isValid('http://www.google.com'));
    }

    public function testIsUrlInvalidWithoutCUrl()
    {
        $this->assertTrue($this->validator->isValid('http://12345'));
    }

    public function testIsUrlInvalidWithoutCUrlAndProtocol()
    {
        $this->assertFalse($this->validator->isValid('12345'));
    }

    public function testIsUrlValidWithCUrl()
    {
        $this->validator->setOptions(array('curl' => true));

        $this->assertTrue($this->validator->isValid('http://www.google.com'));
    }

    public function testIsUrlInvalidWithCUrl()
    {
        $this->validator->setOptions(array('curl' => true));

        $this->assertFalse($this->validator->isValid('http://12345'));
    }
}
