<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

use Acamar\Loader\PSR0Autoloader;

$path = realpath(dirname(__DIR__));

include $path . '/Acamar/Loader/PSR0Autoloader.php';

$autoloader = new PSR0Autoloader();
$autoloader->registerNamespaces(array(
    'Acamar' => $path,
    'tests' => $path
));

$autoloader->register();
