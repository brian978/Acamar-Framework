<?php
/**
 * Acamar Framework
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Validator;

/**
 * Class Password
 *
 * @package Acamar\Validator
 */
class Password extends AbstractValidator
{
    /**
     * @var array
     */
    protected $options = array(
        'length' => 0, // Minimum password length
        'number' => false, // Require numbers
        'ucase' => false, // Require lowercase letters
        'lcase' => false // Require uppercase letters
    );

    /**
     * Checks if the password is valid according to the options provided
     * (aka it checks the complexity using the given options)
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result       = true;
        $failed_count = 0;

        // Validating the length
        if (isset($this->options['length']) && is_numeric($this->options['length']) && $this->options['length'] >= 0) {
            if (strlen(trim($value)) < $this->options['length']) {
                $failed_count++;
            }
        }

        // Validating the characters count
        if ($this->options['number'] == true
            || $this->options['lcase'] == true
            || $this->options['ucase'] == true
        ) {
            // ==== Character counters ==== //
            $lChr   = 0;
            $number = 0;
            $uChr   = 0;

            // ==== Checking each character in the password ==== //
            for ($i = 0; $i < strlen($value); $i++) {
                // ==== Check variables ==== //
                $checked = false;

                // ==== Number check ==== //
                if ($this->options['number'] == true) {
                    if (is_numeric(substr($value, $i, 1))) {
                        $number++;

                        $checked = true;
                    }
                }

                // ==== Lowercase check ==== //
                if ($this->options['lcase'] == true && $checked == false) {
                    if (is_string(substr($value, $i, 1)) && preg_match('/[a-z]/', substr($value, $i, 1))) {
                        $lChr++;

                        $checked = true;
                    }
                }

                // ==== Uppercase check ==== //
                if ($this->options['ucase'] == true && $checked == false) {
                    if (is_string(substr($value, $i, 1)) && preg_match('/[A-Z]/', substr($value, $i, 1))) {
                        $uChr++;
                    }
                }
            }

            if ($this->options['number'] == true && $number == 0) {
                $failed_count++;
            }

            if ($this->options['lcase'] == true && $lChr == 0) {
                $failed_count++;
            }

            if ($this->options['ucase'] == true && $uChr == 0) {
                $failed_count++;
            }
        }

        // ==== Checking the failed count ==== //
        if ($failed_count > 0) {
            $result = false;
        }

        return $result;
    }
}
