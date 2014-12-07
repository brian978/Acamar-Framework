<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc\Router;

/**
 * Interface RouterAwareInterface
 *
 * @package Acamar\Mvc\Router
 */
interface RouterAwareInterface
{
    /**
     * Injects the router in the object
     *
     * @param Router $router
     * @return mixed
     */
    public function setRouter(Router $router);
} 
