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
 * Class Email
 *
 * @package Acamar\Validator
 */
class Email extends AbstractValidator
{
    protected $options = array(
        'check_dns' => false
    );

    /**
     * Validates the provided email address. It can also check the DNS to see if it is valid.
     *
     * @param string $value
     * @throws \RuntimeException
     * @return boolean
     */
    public function isValid($value)
    {
        $isValid = false;
        $email   = filter_var(filter_var($value, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);


        if ($email !== false) {
            $isValid = true;

            // Validating the DNS
            if ($this->options['check_dns'] === true) {
                // Getting the DNS part of the email
                $dns = substr($email, strpos($email, '@'));

                // Checking the DNS depending on what function is available
                if (function_exists('checkdnsrr') && checkdnsrr($dns) === false) {
                    $isValid = false;
                } elseif (function_exists('gethostbyname') && gethostbyname($dns) === $dns) {
                    $isValid = false;
                } else {
                    throw new \RuntimeException(
                        'In order for the domain name to be checked one of
                     the following functions must be available: checkdnsrr, gethostbyname'
                    );
                }
            }
        }

        return $isValid;
    }
}
