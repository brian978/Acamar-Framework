<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Model\Entity;

/**
 * Class XmlEntity
 *
 * @package Acamar\Model\Entity
 */
class XmlEntity extends AbstractEntity
{
    /**
     * @var string
     */
    protected $tag = "";

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $value = "";

    /**
     * @var EntityCollection
     */
    protected $children = null;

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return XmlEntity
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return XmlEntity
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return XmlEntity
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return EntityCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @param EntityCollection $children
     * @return XmlEntity
     */
    public function setChildren(EntityCollection $children)
    {
        $this->children = $children;

        return $this;
    }
}