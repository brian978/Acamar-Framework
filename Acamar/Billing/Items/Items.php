<?php
/**
 * Acamar Framework
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Billing\Items;

/**
 * Class Items
 *
 * @package Acamar\Billing\Items
 *
 * @property string currency
 * @property string category
 */
class Items
{
    /**
     * Array of Item objects
     *
     * @var array
     */
    protected $items = array();

    /**
     * @var array
     */
    protected $attributes = array(
        'currency' => '',
        'category' => '',
    );

    /**
     * Total price of items
     *
     * @var number
     */
    protected $price = 0;

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $value = null;

        if (isset($this->attributes[$name])) {
            $value = $this->attributes[$name];
        }

        return $value;
    }

    /**
     * @return $this
     */
    public function notify()
    {
        $this->price = 0;

        return $this;
    }

    /**
     * Creates a new Item object and returns it
     *
     * @param void
     * @return Item
     */
    public function newItem()
    {
        $item = new Item();

        // Creating the item
        $this->items[] = $item;

        // Setting the observer
        $item->setObserver($this);

        return $item;
    }

    /**
     * Retrieves the items price
     *
     * @param void
     * @return number
     */
    public function getPrice()
    {
        if ($this->price == 0) {
            foreach ($this->items as $item) {
                $this->price += $item->price;
            }
        }

        return $this->price;
    }

    /**
     * Retrieves the array of items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
