<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

use Acamar\Loader\PSR4Autoloader;

$path = realpath(dirname(__DIR__));

include $path . '/Acamar/Loader/PSR4Autoloader.php';

$autoloader = new PSR4Autoloader();
$autoloader->registerNamespaces([
    'Acamar' => $path . '/Acamar',
    'AcamarTest' => __DIR__ . '/AcamarTest',
    'TestHelpers' => __DIR__ . '/TestHelpers'
]);

$autoloader->register();
