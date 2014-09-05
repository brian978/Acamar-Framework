<?php
/**
 * Acamar Framework
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Billing\PayPal;

/**
 * Class PayPal
 *
 * @package Acamar\Billing\PayPal
 */
abstract class PayPal implements PayPalInterface
{
    /**
     * Environment constants
     *
     */
    const ENV_PRODUCTION = 1;
    const ENV_TESTING    = 2;

    /**
     * Platform constants
     *
     */
    const PLATFORM_DESKTOP = 1;
    const PLATFORM_MOBILE  = 2;

    /**
     * An array of supported currencies
     *
     * @var array
     */
    protected $currencies = array(
        'AUD' => true,
        'CAD' => true,
        'CZK' => true,
        'DKK' => true,
        'EUR' => true,
        'HKD' => true,
        'HUF' => true,
        'JPY' => true,
        'NOK' => true,
        'NZD' => true,
        'PLN' => true,
        'GBP' => true,
        'SGD' => true,
        'SEK' => true,
        'CHF' => true,
        'USD' => true,
    );

    /**
     * Generates the HTML tag for the PayPal buttons
     *
     * @param $tag
     * @param $attributes
     * @return string
     */
    protected static function generateHtml($tag, $attributes)
    {
        $html = '<';

        switch ($tag) {
            case 'img':
                $html .= 'img';
                break;
        }

        foreach ($attributes as $attr => $value) {
            $html .= ' ' . $attr . '="' . $value . '"';
        }

        $html = '>';

        return $html;
    }

    /**
     * Returns the image for the PayPal ExpressCheckout button
     *
     * @param void
     * @return string
     */
    protected static function getExpressCheckoutButton()
    {
        $attributes          = array();
        $attributes['src']   = 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif';
        $attributes['style'] = 'margin-right:7px;';

        return self::generateHtml('img', $attributes);
    }

    /**
     * Returns the image for the Digital Goods for ExpressCheckout button
     *
     * @param void
     * @return string
     */
    protected static function getDigitalGoodsButton()
    {
        $attributes        = array();
        $attributes['src'] = 'https://www.paypal.com/en_US/i/btn/btn_dg_pay_w_paypal.gif';

        return self::generateHtml('img', $attributes);
    }

    public function isCurrencySupported($currency)
    {
        // Check var
        $supported = false;

        // Checking if the currency is supported
        if (is_string($currency) && isset($this->currencies[$currency])) {
            $supported = true;
        }

        return $supported;
    }

    /**
     * Sends a POST request to a specified URL
     *
     * @param string $url
     * @param string $query_string The fields used to post the data
     * @return string
     */
    protected function post($url, $query_string)
    {
        // Initializing the cURL session
        $ch = curl_init();

        // Setting the URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // Setting the request type
        curl_setopt($ch, CURLOPT_POST, true);

        // Setting the post fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);

        // Other options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // Executing
        $response = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);

        return $this->processNVPResponse($response);
    }

    /**
     * Processes a response from PayPal and breaks the string into an array
     *
     * @param string $response
     * @return array
     */
    protected function processNVPResponse($response)
    {
        // Result var
        $result = array();

        // Trimming the response string
        $response = trim($response);

        // Checking if the response is a string
        if (is_string($response) && !empty($response)) {
            // Decoding and exploding by "&"
            $response = explode('&', urldecode($response));

            // Going through the response an building a key => value assoc
            foreach ($response as $key_value) {
                // Separating the key from the value
                $kv = explode('=', $key_value);

                // Building the array
                $result[$kv[0]] = $kv[1];
            }
        }

        return $result;
    }
}
