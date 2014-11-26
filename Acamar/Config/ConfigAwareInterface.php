<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Config;

/**
 * Interface ConfigAwareInterface
 *
 * @package Acamar\Config
 */
interface ConfigAwareInterface
{
    /**
     * Inject the configuration object
     *
     * @param Config $config
     * @return mixed
     */
    public function setConfig(Config $config);
}
