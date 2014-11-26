<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Billing\PayPal;

use Acamar\Billing\Exception\InvalidArgumentException;
use Acamar\Billing\Exception\RuntimeException;
use Acamar\Billing\Items\Items;

/**
 * Class ExpressCheckout
 *
 * ----------------------------------------------------------
 * ERROR CODES
 * ----------------------------------------------------------
 *
 * 10 - The required parameters for the SetExpressCheckout method where not present
 * 20 - Invalid currency
 * 30 - PayPal request failed
 * 33 - Could not get response from PayPal
 * 40 - The required parameters for the GetExpressCheckoutDetails method where not present
 * 50 - The required parameters for the DoExpressCheckoutPayment method where not present
 *
 * ----------------------------------------------------------
 *
 * @package Acamar\Billing\PayPal
 */
class ExpressCheckout extends PayPal
{
    /**
     * Internal log
     *
     * @var string
     */
    protected $log = '';

    /**
     * Errors array
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Options array
     *
     * @var array
     */
    protected $options = array();

    /**
     * Selected environment
     *
     * @var string
     */
    protected $environment;

    /**
     * Selected platform
     *
     * @var string
     */
    protected $platform;

    /**
     * Servers for the checkout
     *
     * @var array
     */
    protected $servers = array();

    /**
     * API version
     *
     * @var string
     */
    protected $version = '92.0';

    /**
     * Used to initialize the options and create the object
     *
     * @param array $options
     * @param int $environment [ optional ] Default is "testing"
     * @param int $platform [ optional ] Default is "desktop"
     * @throws \Acamar\Billing\Exception\InvalidArgumentException
     * @return ExpressCheckout
     */
    public function __construct(array $options, $environment = self::ENV_TESTING, $platform = self::PLATFORM_DESKTOP)
    {
        // Checking the environment param to see if it's valid
        if (!in_array($environment, array(self::ENV_PRODUCTION, self::ENV_TESTING))) {
            throw new InvalidArgumentException('The $environment parameter can only be "PayPal::ENV_PRODUCTION" or "PayPal::ENV_TESTING".');
        }

        // Checking the $platform param to see if it's valid
        if (!in_array($platform, array(self::PLATFORM_DESKTOP, self::PLATFORM_MOBILE))) {
            throw new InvalidArgumentException('The $platform parameter can only be "PayPal::PLATFORM_DESKTOP" or "PayPal::PLATFORM_MOBILE".');
        }

        // API options
        $this->options['username']  = '';
        $this->options['password']  = '';
        $this->options['signature'] = '';

        // Overwrite default options
        if (count($options) > 0) {
            $this->options = array_merge($this->options, $options);
        }

        // Initializing the environment
        $this->environment = $environment;

        // Initializing the platform
        $this->platform = $platform;

        // Initializing the servers for PayPal
        $this->initServers();
    }

    /**
     * Initializes the servers used for each environment
     *
     * @param void
     * @return void
     */
    protected function initServers()
    {
        /**
         * ------------------------------------
         * Servers for requests
         * ------------------------------------
         *
         */
        $this->servers['request'] = array(

            self::ENV_PRODUCTION => 'https://api-3t.paypal.com/nvp',
            self::ENV_TESTING => 'https://api-3t.sandbox.paypal.com/nvp'

        );

        /**
         * -----------------------------------------
         * Servers for redirects
         * -----------------------------------------
         *
         */
        if ($this->platform === self::PLATFORM_DESKTOP) {
            $this->servers['redirect'] = array(

                self::ENV_PRODUCTION => 'https://www.paypal.com/webscr?cmd=_express-checkout&token={token}',
                self::ENV_TESTING => 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token={token}'

            );
        } else if ($this->platform === self::PLATFORM_MOBILE) {
            $this->servers['redirect'] = array(

                self::ENV_PRODUCTION => 'https://www.paypal.com/webscr?cmd=_express-checkout-mobile&token={token}',
                self::ENV_TESTING => 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout-mobile&token={token}'

            );
        }
    }

    /**
     * Gets the PayPal ExpressCheckout button
     *
     * @param void
     * @return string
     */
    public function getButton()
    {
        return self::getExpressCheckoutButton();
    }

    /**
     * Returns the URL needed to redirect to PayPal
     *
     * @param Response $response
     * @return string
     */
    public function getUrl(Response $response)
    {
        $url = '#';

        if ($response->TOKEN !== null) {
            $url = $this->getEndpointUrl('redirect', $response->TOKEN);
        }

        return $url;
    }

    /**
     * Returns an URL for the request depending on the $type
     *
     * @param string $type
     * @param string $token [ optional ]
     * @return string
     * @throws \Acamar\Billing\Exception\RuntimeException
     */
    protected function getEndpointUrl($type, $token = '')
    {
        if (!isset($this->servers[$type][$this->environment])) {
            throw new RuntimeException('The requested URL type was not found in the server config');
        }

        $url = $this->servers[$type][$this->environment];

        // Checking if the token is empty or not and replacing
        if (!empty($token)) {
            $url = str_replace('{token}', $token, $url);
        }

        return $url;
    }

    /**
     * Returns the token provided by PayPal
     *
     * @param void
     * @return string
     */
    protected function getToken()
    {
        $token = '';

        if (isset($_GET['token'])) {
            $token = $_GET['token'];
        }

        return $token;
    }

    /**
     * Returns the payerId provided by PayPal
     *
     * @param void
     * @return string
     */
    protected function getPayerId()
    {
        $token = '';

        if (isset($_GET['PayerID'])) {
            $token = $_GET['PayerID'];
        }

        return $token;
    }

    /**
     * Used to initiate the payment flow and redirect to PayPal if the operation was successful
     *
     * @param Items $items
     * @param string $returnUrl
     * @param string $cancelUrl
     * @return mixed Response object on success or false on fail
     * @throws \Acamar\Billing\Exception\RuntimeException
     */
    public function SetExpressCheckout(Items $items, $returnUrl, $cancelUrl)
    {
        $result = false;

        // Checking the required vars
        if (!empty($this->options['username'])
            && !empty($this->options['password'])
            && !empty($this->options['signature'])
            && !empty($items->currency)
            && !empty($items->category) && in_array($items->category, array('Digital', 'Physical'))
            && !empty($returnUrl)
            && !empty($cancelUrl)
            && $this->isCurrencySupported($items->currency)
        ) {
            // Request string
            $request = 'METHOD=SetExpressCheckout'
                . '&VERSION=' . $this->version
                . '&USER=' . $this->options['username']
                . '&PWD=' . $this->options['password']
                . '&SIGNATURE=' . $this->options['signature']
                . '&PAYMENTREQUEST_0_ITEMCATEGORY=' . $items->category
                . '&PAYMENTREQUEST_0_CURRENCYCODE=' . $items->currency
                . '&PAYMENTREQUEST_0_AMT=' . urlencode($items->getPrice())
                . '&PAYMENTREQUEST_0_PAYMENTACTION=Sale'
                . '&RETURNURL=' . urlencode($returnUrl)
                . '&CANCELURL=' . urlencode($cancelUrl);

            // Building the rest of the request using the items object
            foreach ($items->getItems() as $index => $item) {
                // Checking if the item data is valid
                if (!empty($item->name)
                    && !empty($item->desc)
                    && isset($item->price)
                    && !empty($item->quantity)
                ) {
                    $request .= '&L_PAYMENTREQUEST_0_NAME' . $index . '=' . urlencode($item->name)
                        . '&L_PAYMENTREQUEST_0_DESC' . $index . '=' . urlencode($item->desc)
                        . '&L_PAYMENTREQUEST_0_AMT' . $index . '=' . urlencode($item->price)
                        . '&L_PAYMENTREQUEST_0_QTY' . $index . '=' . urlencode($item->quantity);
                }
            }

            // More request options depending on platform
            if ($this->platform === self::PLATFORM_MOBILE) {
                $request .= '&LANDINGPAGE=Login';
            }

            // Doing the cURL to get the response for the request
            $response = $this->post($this->getEndpointUrl('request'), $request);

            if ($this->checkResponse($response) === true) {
                $result = new Response($response);
            }
        }

        return $result;
    }

    /**
     * Used to obtain details about an Express Checkout transaction
     *
     * @param void
     * @return mixed Response object on success or false on fail
     * @throws \Acamar\Billing\Exception\RuntimeException
     */
    public function GetExpressCheckoutDetails()
    {
        $result = false;
        $token  = $this->getToken();

        // Checking the required vars
        if (!empty($this->options['username'])
            && !empty($this->options['password'])
            && !empty($this->options['signature'])
            && !empty($token)
        ) {
            // Request string
            $request = 'METHOD=GetExpressCheckoutDetails'
                . '&VERSION=' . $this->version
                . '&USER=' . $this->options['username']
                . '&PWD=' . $this->options['password']
                . '&SIGNATURE=' . $this->options['signature']
                . '&TOKEN=' . $token;

            // Doing the cURL to get the response for the request
            $response = $this->post($this->getEndpointUrl('request'), $request);

            if ($this->checkResponse($response) === true) {
                $result = new Response($response);
            }
        }

        return $result;
    }

    /**
     * Used to complete an Express Checkout transaction
     *
     * @param Items $items
     * @return mixed Response object on success or false on fail
     * @throws \Acamar\Billing\Exception\RuntimeException
     */
    public function DoExpressCheckoutPayment(Items $items)
    {
        $result  = false;
        $token   = $this->getToken();
        $payerId = $this->getPayerId();

        // Checking the required vars
        if (!empty($this->options['username'])
            && !empty($this->options['password'])
            && !empty($this->options['signature'])
            && !empty($items->currency)
            && !empty($token)
            && !empty($payerId)
        ) {
            // Checking if the currency is supported by PayPal
            if ($this->isCurrencySupported($items->currency)) {
                // Request string
                $request = 'METHOD=DoExpressCheckoutPayment'
                    . '&VERSION=' . $this->version
                    . '&USER=' . $this->options['username']
                    . '&PWD=' . $this->options['password']
                    . '&SIGNATURE=' . $this->options['signature']
                    . '&TOKEN=' . $token
                    . '&PAYERID=' . $payerId
                    . '&PAYMENTREQUEST_0_AMT=' . urlencode($items->getPrice())
                    . '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($items->currency)
                    . '&PAYMENTREQUEST_0_PAYMENTACTION=Sale';

                // Request URL
                $url = $this->getEndpointUrl('request');

                // Doing the cURL to get the response for the request
                $response = $this->post($url, $request);

                if ($this->checkResponse($response) === true) {
                    $result = new Response($response);
                }
            }
        }

        return $result;
    }

    /**
     * Checks a response for errors
     *
     * @param array $response
     * @return boolean
     * @throws \Acamar\Billing\Exception\InvalidArgumentException
     */
    protected function checkResponse($response)
    {
        $status = false;

        if (is_array($response) && !empty($response)) {
            // Checking if we have a response in the array
            if (count($response) > 0 && $response['ACK'] === 'Success') {
                $status = true;
            }
        } else {
            throw new InvalidArgumentException('The response from PayPal is invalid.');
        }

        return $status;
    }
}
