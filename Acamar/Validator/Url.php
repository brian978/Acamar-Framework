<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Validator;

/**
 * Class Url
 *
 * @package Acamar\Validator
 */
class Url extends AbstractValidator
{
    /**
     * @var array
     */
    protected $options = array(
        'curl' => false // It is used by the isValid method
    );

    /**
     * Validates a given URL. It can also check if the URL is accessible (on by default).
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $isValid        = false;
        $sanitizedValue = filter_var($value, FILTER_SANITIZE_URL);
        $url            = filter_var($sanitizedValue, FILTER_VALIDATE_URL);

        if ($url !== false) {
            $isValid = true;

            // Should we check to see if the URL also exists
            if ($this->options['curl'] === true) {
                // Making the cURL to check if the URL exists
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);

                // If we close the cURL before we get the info we won't find out the HTTP code
                $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                if ($code !== 200) {
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }
}
