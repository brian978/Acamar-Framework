<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

use Acamar\Loader\PSR0Autoloader;

$path = realpath(dirname(__DIR__));

include $path . '/Acamar/Loader/PSR0Autoloader.php';

$autoloader = new PSR0Autoloader();
$autoloader->registerNamespaces([
    'Acamar' => $path,
    'AcamarTest' => __DIR__,
    'TestHelpers' => __DIR__
]);

$autoloader->register();
