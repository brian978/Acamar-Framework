<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc;

/**
 * Interface ApplicationInterface
 *
 * @package Acamar\Mvc
 */
interface ApplicationInterface
{
    /**
     * @return \Acamar\Config\Config
     */
    public function getConfig();

    /**
     * @return \Acamar\Mvc\Router\Router
     */
    public function getRouter();
} 
