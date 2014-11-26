<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Billing\Items;

/**
 * Class Item
 *
 * @package Acamar\Billing\Items
 *
 * @property number price
 * @property string name
 * @property string desc
 * @property number quantity
 */
class Item
{
    /**
     * Properties of the item object
     *
     * @var array
     */
    protected $properties = array(
        'name' => '',
        'desc' => '',
        'price' => '',
        'quantity' => ''
    );

    /**
     * A list of type for each property
     *
     * @var array
     */
    protected $filters = array(
        'name' => 'string',
        'desc' => 'string',
        'price' => 'number',
        'quantity' => 'int'
    );

    /**
     * Items object
     *
     * @var \Acamar\Billing\Items\Items
     */
    protected $observer;

    /**
     * Sets the items object
     *
     * @param \Acamar\Billing\Items\Items $items
     * @return \Acamar\Billing\Items\Item
     */
    public function setObserver(Items $items)
    {
        $this->observer = $items;

        return $this;
    }

    /**
     * Getter method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $value = null;

        if (isset($this->properties[$name])) {
            $value = $this->properties[$name];
        }

        return $value;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if ($this->validate($name, $value)) {
            $this->properties[$name] = $value;

            $this->observer->notify();
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    protected function validate($name, $value)
    {
        $result = true;

        if (isset($this->filters[$name])) {
            switch ($this->filters[$name]) {
                case 'string':
                    if (!is_string($value)) {
                        $result = false;
                    }
                    break;

                case 'number':
                    if (!is_numeric($value)) {
                        $result = false;
                    }
                    break;

                case 'int':
                    if (!is_int($value)) {
                        $result = false;
                    }
                    break;
            }
        }

        return $result;
    }
}
