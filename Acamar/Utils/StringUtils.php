<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Utils;

/**
 * Class StringUtils
 *
 * @package Acamar\Utils
 */
class StringUtils
{
    /**
     * Detects the EOL of a string
     *
     * @param string $string
     * @return string
     */
    public static function detectEol($string)
    {
        // Order is important
        $list = array(
            "CRLF" => "\r\n",
            "LF" => "\n",
            "CR" => "\r"
        );

        foreach ($list as $alias => $eol) {
            if (false !== strstr($string, $eol, true)) {
               return $eol;
            }
        }

        return PHP_EOL;
    }
}