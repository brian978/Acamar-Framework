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
 * Interface ValidatorInterface
 *
 * @package Acamar\Validator
 */
interface ValidatorInterface
{
    /**
     * Validates a given value
     *
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value);
}
