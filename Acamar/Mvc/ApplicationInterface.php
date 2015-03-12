<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
