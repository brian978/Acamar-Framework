<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Config;

/**
 * Class Config
 *
 * @package Acamar\Config
 */
class Config extends \ArrayObject
{
    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function add(array $config)
    {
        parent::exchangeArray($this->mergeArrays($this->getArrayCopy(), $config));

        return $this;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function merge(Config $config)
    {
        parent::exchangeArray($this->mergeArrays($this->getArrayCopy(), $config->getArrayCopy()));

        return $this;
    }

    /**
     * Recursively merge to arrays
     *
     * @param array $config1
     * @param array $config2
     *
     * @return array
     */
    protected function mergeArrays($config1, $config2)
    {
        foreach ($config2 as $key => $value) {
            if (isset($config1[$key])) {
                if (is_array($value) && is_array($config1[$key])) {
                    $config1[$key] = $this->mergeArrays($config1[$key], $value);
                } else {
                    $config1[$key] = $value;
                }
            } else {
                $config1[$key] = $value;
            }
        }

        return $config1;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        $value = null;

        if (parent::offsetExists($name)) {
            $value = parent::offsetGet($name);
        }

        return $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return parent::offsetExists($name);
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $result = parent::getArrayCopy();

        foreach ($result as $index => $value) {
            if ($value instanceof Config) {
                $result[$index] = $value->getArrayCopy();
            }
        }

        return $result;
    }
}
