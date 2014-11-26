<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Validator;

use Acamar\Validator\Email;
use PHPUnit_Framework_TestCase;

/**
 * Class EmailTest
 *
 * @package AcamarTest\Validator
 */
class EmailTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Acamar\Validator\Email
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Email();
    }

    public function testValidationFail()
    {
        $this->assertFalse($this->validator->isValid('asdf@'));
    }

    public function testValidationFailFakeDomain()
    {
        $this->assertTrue($this->validator->isValid('asdf@asda.com'));
    }

    public function testValidationFailFakeDomainWithDnsCheck()
    {
        $this->validator->setOptions(array('check_dns' => true));

        $this->assertFalse($this->validator->isValid('asdf@a11sda.com', true));
    }
}
