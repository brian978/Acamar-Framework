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
 * Class Response
 *
 * @package Acamar\Billing\PayPal
 *
 * @property string TOKEN
 * @property string ACK
 * @property string PAYERID
 * @property string PAYERSTATUS
 * @property string COUNTRYCODE
 * @property string EMAIL
 */
class Response extends \ArrayIterator
{
    /**
     * Constructor
     *
     * @param array $response
     * @return \Acamar\Billing\PayPal\Response
     */
    public function __construct($response)
    {
        parent::__construct($response);
    }

    /**
     * Getter method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $value = null;

        if (parent::offsetExists($name)) {
            $value = parent::offsetGet($name);
        }

        return $value;
    }
}
