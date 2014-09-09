<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Validator;

use Acamar\Validator\Password;
use PHPUnit_Framework_TestCase;

/**
 * Class PasswordTest
 *
 * @package AcamarTest\Validator
 */
class PasswordTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Acamar\Validator\Password
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Password();
    }

    public function testDefaultValidation()
    {
        $this->assertTrue($this->validator->isValid('Asdf1231'));
    }

    public function testNumbersValidationFail()
    {
        $this->validator->setOptions(array(
            'number' => true,
            'ucase' => false,
            'lcase' => false,
            'length' => false,
        ));

        $this->assertFalse($this->validator->isValid('asdfasdasd'));
    }

    public function testUpperCaseValidationFail()
    {
        $this->validator->setOptions(array(
            'number' => false,
            'ucase' => true,
            'lcase' => false,
            'length' => false,
        ));

        $this->assertFalse($this->validator->isValid('sdfasdasd123'));
    }

    public function testLowerCaseValidationFail()
    {
        $this->validator->setOptions(array(
            'number' => false,
            'ucase' => false,
            'lcase' => true,
            'length' => false,
        ));

        $this->assertFalse($this->validator->isValid('ADSDSADGFD123'));
    }

    public function testLengthValidationFail()
    {
        $this->validator->setOptions(array(
            'number' => false,
            'ucase' => false,
            'lcase' => false,
            'length' => 5,
        ));

        $this->assertFalse($this->validator->isValid('Ad1'));
    }

    public function testNoValidation()
    {
        $this->validator->setOptions(array(
            'number' => false,
            'ucase' => false,
            'lcase' => false,
            'length' => false,
        ));

        $this->assertTrue($this->validator->isValid(''));
    }
}
