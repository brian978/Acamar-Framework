<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Model\Mapper;

use Acamar\Model\Entity\EntityCollection;
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
     * Extracts the attributes of an XML element (used when populating object)
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
     * Populates a DOMElement with attributes (used when extracting to XML)
     *
     * @param \DOMElement $element
     * @param array $attributes
     * @return \DOMElement
     */
    protected static function populateAttributes(\DOMElement $element, array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }

        return $element;
    }

    /**
     * Creates an object and sets the XML tag and attributes
     *
     * @param \SimpleXMLElement $data
     * @param array $map
     * @return XmlEntity
     */
    protected function createObject(\SimpleXMLElement $data, array $map)
    {
        /** @var XmlEntity $object */
        $object = $this->createEntityObject($map["entity"]);

        // Validating the object type
        if (!$object instanceof XmlEntity) {
            throw new \RuntimeException("The created object is not of type \\Acamar\\Model\\Entity\\XmlEntity");
        }

        $object->setTag($data->getName());
        $object->setAttributes(static::extractAttributes($data));

        return $object;
    }

    /**
     * The method converts a string or \SimpleXMLElement $object to a set of objects
     *
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
                    } else {
                        $objectProperty = $specs[$childTag];
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
        // Unused parameters
        unset($map);

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
     * The method does the opposite of the populate method
     *
     * @param \Acamar\Model\Entity\EntityInterface|\Acamar\Model\Entity\XmlEntity $object
     * @param string|array $map
     * @param \DOMDocument $document
     * @return string
     */
    public function extract(EntityInterface $object, $map = 'default', \DOMDocument $document = null)
    {
        if ($object instanceof XmlEntity === false) {
            return "";
        }

        // Selecting the map from the ones available
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        if (null === $document) {
            $document = new \DOMDocument();
            $document->preserveWhiteSpace = false;
            $document->formatOutput = true;
        }

        // We can't use the extract method recursively because it needs to output a string and not an object
        $document->appendChild($this->extractElement($object, $map, $document));

        return $document->saveXML();
    }

    /**
     * Extracts a \DOMElement using the data found in the $object
     *
     * @param XmlEntity $object
     * @param string|array $map
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    protected function extractElement(XmlEntity $object, $map, \DOMDocument $document)
    {
        // Selecting the map from the ones available
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        // Creating the element for the $object that is attached to the document
        $element = static::populateAttributes($document->createElement($object->getTag()), $object->getAttributes());

        if ($object->getValue() !== "") {
            $element->nodeValue = $object->getValue();
        } else {
            // For code correctness
            if (!isset($map["specs"])) {
                $map["specs"] = [];
            }

            // Adding a default mapping in case the $object->hasChildren()
            if (!isset($map["specs"]["children"])) {
                $map["specs"]["children"] = "children";
            }

            // Extracting the data according to the specs
            foreach ($map["specs"] as $property) {
                if (is_array($property)) {
                    $property = $property["toProperty"];
                }

                // Extracting the data from the property
                $this->extractFromProperty($property, $object, $document, $element);
            }
        }

        return $element;
    }

    /**
     * Extracts data from a property of the object
     *
     * @param string $property
     * @param XmlEntity $object
     * @param \DOMDocument $document
     * @param \DOMElement $element
     */
    protected function extractFromProperty($property, XmlEntity $object, \DOMDocument $document, \DOMElement $element)
    {
        $methodName = $this->createGetterNameFromPropertyName($property);
        $value = $object->$methodName();

        if ($value instanceof EntityCollection) {
            /** @var XmlEntity $child */
            foreach ($value as $child) {
                $childNode = $this->extractElement($child, $child->getTag(), $document);
                if (!empty($childNode)) {
                    $element->appendChild($childNode);
                }
            }
        } else if ($value instanceof XmlEntity) {
            $childNode = $this->extractElement($value, $value->getTag(), $document);
            if (!empty($childNode)) {
                $element->appendChild($childNode);
            }
        }
    }

    /**
     *
     * @param EntityCollectionInterface $collection
     * @param string|array $map
     * @return array
     */
    public function extractCollection(EntityCollectionInterface $collection, $map = 'default')
    {
        throw new \RuntimeException("This method is not available in the XmlMapper");
    }
}
