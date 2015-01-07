<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Model\Mapper;

use Acamar\Model\Entity\EntityCollection;
use Acamar\Model\Entity\EntityCollectionInterface;
use Acamar\Model\Entity\EntityInterface;
use Acamar\Model\Mapper\Exception\WrongDataTypeException;

/**
 * Class AbstractMapper
 *
 * @package Acamar\Model\Mapper
 */
class ObjectMapper implements MapperInterface
{
    /**
     * @var MapCollection
     */
    protected $mapCollection = null;

    /**
     * @var \Acamar\Model\Entity\EntityCollectionInterface
     */
    protected $collectionPrototype = null;

    /**
     * This is sort of a cache to avoid creating new objects each time
     *
     * @var array
     */
    protected $entityPrototypes = array();

    /**
     * The callable methods property acts like a cache for the setProperty() method
     *
     * @var array
     */
    protected $callableMethods = array();

    /**
     * @var \SplObjectStorage
     */
    protected $objectClasses = null;

    /**
     * @param MapCollection $mapCollection
     */
    public function __construct(MapCollection $mapCollection)
    {
        $this->mapCollection = $mapCollection;
        $this->objectClasses = new \SplObjectStorage();
    }

    /**
     *
     * @param EntityCollectionInterface $collectionPrototype
     * @return $this
     */
    public function setCollectionPrototype(EntityCollectionInterface $collectionPrototype)
    {
        $this->collectionPrototype = $collectionPrototype;

        return $this;
    }

    /**
     *
     * @return EntityCollection
     */
    public function getCollectionPrototype()
    {
        if ($this->collectionPrototype === null) {
            $this->collectionPrototype = new EntityCollection();
        }

        return $this->collectionPrototype;
    }

    /**
     * @param string $name
     * @return array
     */
    public function findMap($name)
    {
        return $this->mapCollection->findMap($name);
    }

    /**
     * @param string $className
     * @throws \RuntimeException
     * @return EntityInterface
     */
    public function createEntityObject($className)
    {
        /** @var $entity EntityInterface */
        if (!isset($this->entityPrototypes[$className])) {
            $entity = new $className();
            if ($entity instanceof EntityInterface) {
                $this->entityPrototypes[$className] = $entity;
            } else {
                throw new \RuntimeException(
                    'The class for the entity must implement \Acamar\Model\Entity\EntityInterface'
                );
            }
        }

        return clone $this->entityPrototypes[$className];
    }

    /**
     *
     * @param string $property
     * @return mixed
     */
    protected function createSetterNameFromPropertyName($property)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($string) {
                return ucfirst($string);
            },
            'set' . ucfirst($property)
        );
    }

    /**
     *
     * @param string $property
     * @return mixed
     */
    protected function createGetterNameFromPropertyName($property)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($string) {
                return ucfirst($string);
            },
            'get' . ucfirst($property)
        );
    }

    /**
     * Returns all the data we have about an objects' methods
     *
     * @param EntityInterface $object
     * @return array
     */
    protected function & getObjectMethodsData(EntityInterface $object)
    {
        try {
            $objectClass = $this->objectClasses->offsetGet($object);
        } catch (\UnexpectedValueException $e) {
            $objectClass = get_class($object);
            $this->objectClasses->attach($object, $objectClass);
        }

        if (!isset($this->callableMethods[$objectClass])) {
            $this->callableMethods[$objectClass] = [];
        }

        return $this->callableMethods[$objectClass];
    }

    /**
     * Checks if the property of a given object is a collection or not
     *
     * @param EntityInterface $object
     * @param string $propertyName
     * @return bool
     */
    protected function isCollection(EntityInterface $object, $propertyName)
    {
        // Calling the setter and registering it in the callableMethods property for future use
        $callableMethods = $this->getObjectMethodsData($object);
        if (!isset($callableMethods[$propertyName])) {
            $this->extractMethodData($callableMethods, $object, $propertyName);
        }

        if (is_array($callableMethods[$propertyName])) {
            return null !== $callableMethods[$propertyName]['collection'];
        }

        return false;
    }

    /**
     * Stores different information about a callable/uncallable method
     *
     * @param array $callableMethods
     * @param EntityInterface $object
     * @param string $propertyName
     * @return $this
     */
    protected function extractMethodData(array & $callableMethods, EntityInterface $object, $propertyName)
    {
        $methodName = $this->createSetterNameFromPropertyName($propertyName);
        if (is_callable(array($object, $methodName))) {
            // We need to determine if we have a hinted parameter (for a collection)
            // We also only care about the first parameter since that is the only one we use
            $reflection           = new \ReflectionObject($object);
            $reflectionMethod     = $reflection->getMethod($methodName);
            $reflectionParameters = $reflectionMethod->getParameters();
            $reflectionClass      = $reflectionParameters[0]->getClass();

            // Checking if we have to insert a collection into the object's property
            $collectionPrototype = null;
            if ($reflectionClass instanceof \ReflectionClass
                && $reflectionClass->isInstance($this->getCollectionPrototype())
            ) {
                $collectionPrototype = $reflectionClass->newInstance();
            }

            // Caching our info so it's faster next time
            $callableMethods[$propertyName] = array("method" => $methodName, "collection" => $collectionPrototype);
        } else {
            // So we don't call the method we need to reset it to null
            $methodName = null;

            // We set this to false so we don't create the setter name again next time
            // since the method will still be not callable
            $callableMethods[$propertyName] = false;
        }

        return $this;
    }

    /**
     * Populates a property of an object
     *
     * @param string $objectClass
     * @param EntityInterface $object
     * @param string $propertyName
     * @param string|EntityInterface $value
     */
    protected function setProperty($objectClass, EntityInterface $object, $propertyName, $value)
    {
        if (!isset($this->callableMethods[$objectClass])) {
            $this->callableMethods[$objectClass] = [];
        }

        $callableMethods     = & $this->callableMethods[$objectClass];
        $methodName          = null;
        $collectionPrototype = null;

        // Making sure we have information about the callable method
        $callableMethods = $this->getObjectMethodsData($object);
        if (!isset($callableMethods[$propertyName])) {
            $this->extractMethodData($callableMethods, $object, $propertyName);
        }

        // Now we can get what data we need from the $callableMethods array to populate the property
        if ($callableMethods[$propertyName] !== false) {
            $methodName = $callableMethods[$propertyName]["method"];

            // Getting the collection prototype from cache
            $collectionPrototype = null;
            if (!$value instanceof EntityCollectionInterface && $callableMethods[$propertyName]["collection"] !== null) {
                /** @var $collection EntityCollectionInterface */
                $collectionPrototype = $callableMethods[$propertyName]["collection"];
            }
        }

        // Populating the property accordingly (if we have a method to call for the property)
        if ($methodName !== null) {
            if ($collectionPrototype !== null) {
                $collection = clone $collectionPrototype;
                $collection->add($value);
                $object->$methodName($collection);
            } else {
                $object->$methodName($value);
            }
        }
    }

    /**
     * The method is used to populate a single entity with a set of data
     *
     * @param array|\ArrayIterator $data
     * @param string|array $map
     * @param EntityInterface $object
     * @throws Exception\WrongDataTypeException
     * @return EntityInterface|null
     */
    public function populate($data, $map = 'default', EntityInterface $object = null)
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        // Getting the map if it's a string
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        if (!is_array($map) || !isset($map['entity']) || !isset($map['specs'])) {
            return $object;
        }

        // Using the identification field to determine if we should populate the object or not
        $identField = (isset($map['identField']) ? $map['identField'] : null);
        if ($identField !== null && count($this->getIdentificationData($map, $data)) == 0) {
            return $object;
        }

        // Some data from the map (shortcuts)
        $specs       = $map['specs'];
        $objectClass = $map['entity'];

        // We don't need to create the object if we can't identify it
        if ($object === null) {
            $object = $this->createEntityObject($objectClass);
        }

        // Populating the object
        foreach ($data as $key => $value) {
            if (isset($specs[$key])) {
                $property = $specs[$key];
                if (is_string($property)) {
                    $this->setProperty($objectClass, $object, $property, $value);
                } elseif (is_array($property) && isset($property['toProperty'])) {
                    if (isset($property['map'])) {
                        if (!$this->isCollection($object, $property['toProperty'])) {
                            $childObject = $this->populate($data, $property['map']);
                            if ($childObject !== null) {
                                $this->setProperty($objectClass, $object, $property['toProperty'], $childObject);
                            }
                        } else {
                            $methodName = $this->createGetterNameFromPropertyName($property['toProperty']);
                            if ($object->$methodName() instanceof EntityCollectionInterface) {
                                $this->populateCollection(array($data), $property['map'], $object->$methodName());
                            } else {
                                $collection = $this->populateCollection(array($data), $property['map']);
                                if (!empty($collection)) {
                                    $this->setProperty($objectClass, $object, $property['toProperty'], $collection);
                                }
                            }
                        }
                    } else {
                        $this->setProperty($objectClass, $object, $property['toProperty'], $value);
                    }
                }
            }
        }

        return $object;
    }

    /**
     * The method is used to map a series of data to multiple entities and store them in a collection
     *
     * @param array $data
     * @param string|array $map
     * @param EntityCollectionInterface $collection
     * @return EntityCollectionInterface
     * @throws Exception\WrongDataTypeException
     */
    public function populateCollection($data, $map = "default", EntityCollectionInterface $collection = null)
    {
        if (!is_array($data) && $data instanceof \ArrayIterator === false) {
            $message = 'The $data argument must be either an array or an instance of \ArrayIterator';
            $message .= gettype($data) . ' given';

            throw new WrongDataTypeException($message);
        }

        // Getting the map if it's a string
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        if ($collection === null) {
            $collection = clone $this->getCollectionPrototype();
        }

        // We we have no mapping information or a way to identify the created objects then we can't map anything
        if (!is_array($map)
            || !isset($map['identField'])
            || (
                !is_string($map['identField'])
                && !is_array($map['identField'])
            )
        ) {
            return $collection;
        }

        foreach ($data as $part) {
            // Locating the main object (if there is one)
            $object = null;
            if ($collection->count() > 0) {
                $object = $this->locateInCollection($collection, $map, $part);
            }

            if ($object !== null) {
                // Locating the collections in the objects to pass the data to them as well
                $specs = $map['specs'];
                foreach ($specs as $propertyName) {
                    if (is_array($propertyName) && isset($propertyName['toProperty'])) {
                        if (isset($propertyName['map'])) {
                            $methodName = $this->createGetterNameFromPropertyName($propertyName['toProperty']);
                            if ($object->$methodName() instanceof EntityCollectionInterface) {
                                $this->populateCollection(array($part), $propertyName['map'], $object->$methodName());
                            }
                        } else {
                            $this->populate($part, $map, $object);
                        }
                    }
                }
            } else {
                $object = $this->populate($part, $map);
                if (null !== $object) {
                    $collection->add($object);
                }
            }
        }

        return $collection;
    }

    /**
     * This method is called by the populateCollection() method in order to locate an already existing entity
     *
     * @param EntityCollectionInterface $collection
     * @param array $map
     * @param array $data
     * @return null|EntityInterface
     */
    protected function locateInCollection(EntityCollectionInterface $collection, $map, $data)
    {
        // Getting the data that will help us identify the object in the collection
        $idData = $this->getIdentificationData($map, $data);

        // Locating the object in the collection
        if (!empty($idData)) {
            $idData = $this->convertIdentificationData($idData);

            // Locating the object that has the identification data we extracted
            foreach ($collection as $object) {
                $matched = true;
                foreach ($idData as $methodName => $value) {
                    if ($object->$methodName() != $value) {
                        $matched = false;
                    }
                }

                // All the values of the methods must be matched to consider the object as "found"
                if ($matched) {
                    return $object;
                }
            }
        }

        return null;
    }

    /**
     * @param \Acamar\Model\Entity\EntityInterface $object
     * @param string|array $map
     * @return array
     */
    public function extract(EntityInterface $object, $map = 'default')
    {
        $result = array();

        // Selecting the map from the ones available
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        // No need to continue if we have no map
        if (!is_array($map) || !isset($map['specs'])) {
            return $result;
        }

        // We need to flip the values and the field names in the map because
        // we need to do the reverse operation of the populate
        $reversedMap = $this->mapCollection->flip($map);

        // Extracting the first layer of the results
        $tmpResult = $object->toArray();

        // Creating the result
        foreach ($tmpResult as $field => $value) {
            if ($value instanceof EntityInterface) {
                $extracted = $this->extract($value, $this->findMapForField($field, $map));
                $result    = array_merge($result, $extracted);
            } else {
                // We only need to extract the fields that are in the map
                // (the populate() method does the exact thing - only sets data that is in the map)
                if (isset($reversedMap[$field])) {
                    $result[$reversedMap[$field]] = $value;
                }
            }
        }

        return $result;
    }

    /**
     *
     * @param EntityCollectionInterface $collection
     * @param string|array $map
     * @return array
     */
    public function extractCollection(EntityCollectionInterface $collection, $map = 'default')
    {
        $result = array();

        // Selecting the map from the ones available
        if (is_string($map)) {
            $map = $this->findMap($map);
        }

        // No need to continue if we have no map
        if (!is_array($map) || !isset($map['specs'])) {
            return $result;
        }

        foreach ($collection as $object) {
            $collectionData = [];

            // The object data will contain child object but will ignore collections
            $objectData = $this->extract($object, $map);

            // Locating the potential collections in the current object since what we extracted
            // will not contain them
            foreach ($map['specs'] as $toField) {
                if (is_array($toField)) {
                    $methodName    = $this->createGetterNameFromPropertyName($toField['toProperty']);
                    $propertyValue = $object->$methodName();
                    if ($propertyValue instanceof EntityCollectionInterface) {
                        $extractedData = $this->extractCollection($propertyValue, $toField['map']);

                        if (empty($collectionData)) {
                            $collectionData = $extractedData;
                        } else {
                            foreach ($collectionData as $idx => &$data) {
                                if (isset($extractedData[$idx])) {
                                    $data = array_merge($data, $extractedData[$idx]);
                                }
                            }
                        }
                    } elseif (is_array($propertyValue)) {
                        foreach ($propertyValue as $value) {
                            $collectionData[] = array($toField['toProperty'] => $value);
                        }
                    }
                }
            }

            if (count($collectionData) > 0) {
                foreach ($collectionData as $childObjData) {
                    $result[] = array_merge($objectData, $childObjData);
                }
            } else {
                $result[] = $objectData;
            }
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @param array $map
     * @return string
     */
    protected function findMapForField($fieldName, $map)
    {
        foreach ($map['specs'] as $toSpecs) {
            if (is_array($toSpecs) && $toSpecs['toProperty'] === $fieldName) {
                return $toSpecs['map'];
            }
        }

        return '';
    }

    /**
     * The method will return an array with the identification data (if all the data was found)
     * or it will return an empty array if it found only part of the identification data or not at all
     *
     * @param array $map
     * @param array $data
     * @return array
     */
    protected function getIdentificationData($map, $data)
    {
        $specs = $map['specs'];

        // Getting the data that will help us identify the object in the collection
        $idData = [];

        if (is_array($map['identField'])) {
            foreach ($map['identField'] as $identField) {
                if (isset($specs[$identField])
                    && is_string($specs[$identField])
                    && isset($data[$identField])
                    && $data[$identField] != null
                ) {
                    $idData[$specs[$identField]] = $data[$identField];
                }
            }
        } else if (is_string($map['identField'])) {
            if (isset($specs[$map['identField']])
                && is_string($specs[$map['identField']])
                && isset($data[$map['identField']])
                && $data[$map['identField']] != null
            ) {
                $idData[$specs[$map['identField']]] = $data[$map['identField']];
            }
        }

        if (count($idData) == count($map['identField'])) {
            return $idData;
        }

        return [];
    }

    /**
     * Converts the property names from the identification data to method names
     *
     * @param array $data
     * @return array
     */
    protected function convertIdentificationData(array $data)
    {
        $tmpIdData = $data;
        $data      = [];

        // We need to convert the property names in the identification data to method names
        // We don't call this in the foreach loop below because the call to the createGetterNameFromPropertyName()
        // method is very expensive in terms of performance
        foreach ($tmpIdData as $propertyName => $value) {
            $data[$this->createGetterNameFromPropertyName($propertyName)] = $value;
        }

        return $data;
    }
}
