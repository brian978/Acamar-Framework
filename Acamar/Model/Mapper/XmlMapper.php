<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Model\Mapper;

use Acamar\Model\Entity\EntityCollectionInterface;
use Acamar\Model\Entity\EntityInterface;
use Acamar\Model\Entity\XmlEntity;
use Acamar\Model\Mapper\Exception\WrongDataTypeException;

/**
 * Class XmlMapper
 *
 * @package Acamar\Model\Mapper
 */
class XmlMapper extends ArrayMapper
{
    /**
     * This is used when a map is not found for a tag
     *
     * @var array
     */
    private $defaultMap = [
        "entity" => "\\Acamar\\Model\\Entity\\XmlEntity",
        "specs" => []
    ];

    /**
     * @param string $name
     * @return array
     */
    public function findMap($name)
    {
        $map = $this->mapCollection->findMap($name);
        if (empty($map)) {
            $map = $this->defaultMap;
        }

        return $map;
    }

    /**
     * Extracts the attributes of an XML element
     *
     * @param \SimpleXMLElement $element
     * @return array
     */
    protected static function extractAttributes(\SimpleXMLElement $element)
    {
        $attributes = json_decode(json_encode($element->attributes()), true);

        if (isset($attributes["@attributes"]) && is_array($attributes["@attributes"])) {
            return $attributes["@attributes"];
        }

        return [];
    }

    /**
     *
     * @param \SimpleXMLElement $data
     * @param array $map
     * @return XmlEntity
     */
    protected function createObject(\SimpleXMLElement $data, array $map)
    {
        /** @var XmlEntity $object */
        $object = $this->createEntityObject($map["entity"]);

        // Tag
        $object->setTag($data->getName());

        // Attributes
        $object->setAttributes(static::extractAttributes($data));

        return $object;
    }

    /**
     * @param string|\SimpleXMLElement $data
     * @param string $map
     * @param EntityInterface|null $object
     * @return XmlEntity
     * @throws WrongDataTypeException
     */
    public function populate($data, $map = 'default', EntityInterface $object = null)
    {
        if (!is_string($data) && $data instanceof \SimpleXMLElement === false) {
            $message = 'The $data argument must be either a string or an instance of \SimpleXMLElement';
            $message .= ' ' . gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        if (is_string($data)) {
            $data = new \SimpleXMLElement($data);
        }

        // Loading the map
        $map = $this->findMap($map);

        // Shortcuts
        $objectClass = &$map["entity"];
        $specs = &$map["specs"];

        /** @var XmlEntity $object */
        $object = $this->createEntityObject($map["entity"]);

        /**
         * Populating the object defaults
         */
        // Tag
        $object->setTag($data->getName());

        // Attributes
        $object->setAttributes(static::extractAttributes($data));

        /**
         * Going through the children and mapping them
         */
        if (0 !== $data->count()) {
            foreach ($data as $child) {
                /** @var \SimpleXMLElement $child */
                $childTag = $child->getName();

                // Mapping defaults
                $objectProperty = "children";
                $childMap = "default";

                // Trying to find something to map
                if (isset($specs[$childTag])) {
                    if (is_array($specs[$childTag])) {
                        if (isset($specs[$childTag]["map"])) {
                            $childMap = $specs[$childTag]["map"];
                        }

                        if (isset($specs[$childTag]["toProperty"])) {
                            $objectProperty = $specs[$childTag]["toProperty"];
                        }
                    }
                }

                // Mapping the data
                if (!$this->isCollection($object, $objectProperty)) {
                    $childObject = $this->populate($child, $childMap);
                    if ($childObject !== null) {
                        $this->setProperty($objectClass, $object, $objectProperty, $childObject);
                    }
                } else {
                    $methodName = $this->createGetterNameFromPropertyName($objectProperty);
                    if ($object->$methodName() instanceof EntityCollectionInterface) {
                        $this->populateCollection($child, $childMap, $object->$methodName());
                    } else {
                        $collection = $this->populateCollection($child, $childMap);
                        if (!empty($collection)) {
                            $this->setProperty($objectClass, $object, $objectProperty, $collection);
                        }
                    }
                }
            }
        } else {
            $object->setValue((string)$data);
        }

        return $object;
    }

    /**
     * The method is used to map a series of data to multiple entities and store them in a collection
     *
     * @param string|\SimpleXMLElement $data
     * @param string|array $map
     * @param EntityCollectionInterface $collection
     * @return EntityCollectionInterface
     * @throws Exception\WrongDataTypeException
     */
    public function populateCollection($data, $map = "default", EntityCollectionInterface $collection = null)
    {
        if (!is_string($data) && $data instanceof \SimpleXMLElement === false) {
            $message = 'The $data argument must be either a string or an instance of \SimpleXMLElement';
            $message .= ' ' . gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        if ($collection === null) {
            $collection = clone $this->getCollectionPrototype();
        }

        if (is_string($data)) {
            $data = new \SimpleXMLElement($data);
        }

        $object = $this->populate($data, $data->getName());
        if ($object->getTag() !== "") {
            $collection->add($object);
        }

        return $collection;
    }

    /**
     * @param \Acamar\Model\Entity\EntityInterface|\Acamar\Model\Entity\XmlEntity $object
     * @param string|array $map
     * @return string
     */
    public function extract(EntityInterface $object, $map = 'default')
    {
        $result = "<?xml version=\"1.0\"?>";
        if ($object instanceof XmlEntity === false) {
            return $result;
        }

        // Selecting the map from the ones available
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        // We need to flip the values and the field names in the map because
        // we need to do the reverse operation of the populate
        $reversedMap = $this->mapCollection->flip($map);


        return $result;
    }
}
