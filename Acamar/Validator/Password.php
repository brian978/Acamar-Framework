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
    protected $options = array(
        'length' => 8, // Minimum password length
        'number' => true, // Require numbers
        'ucase' => true, // Require lowercase letters
        'lcase' => true // Require uppercase letters
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
        // ==== Result variable ==== //
        $result = true;

        // ==== Check variable ==== //
        $failed_count = 0;

        // ==== Checking if the length check is enabled ==== //
        if (isset($this->options['length'])
            && is_numeric($this->options['length'])
            && $this->options['length'] > 0
        ) {
            // ==== Checking the length ==== //
            if (strlen(trim($value)) < $this->options['length']) {
                $failed_count++;
            }
        }

        // ==== Checking if the number or lowercase or uppercase check is active ==== //
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

            // ==== Checking number count ==== //
            if ($this->options['number'] == true && $number == 0) {
                $failed_count++;
            }

            // ==== Checking lowercase count ==== //
            if ($this->options['lcase'] == true && $lChr == 0) {
                $failed_count++;
            }

            // ==== Checking uppercase count ==== //
            if ($this->options['ucase'] == true && $uChr == 0) {
                $failed_count++;
            }
        }

        // ==== Checking the failed count ==== //
        if ($failed_count != 0) {
            $result = false;
        }

        // ==== Returning result ==== //
        return $result;
    }
}
