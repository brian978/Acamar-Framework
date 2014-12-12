<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
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
