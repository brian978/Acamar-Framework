<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Model\Mapper;

use Acamar\Model\Entity\EntityInterface;

/**
 * Class XmlMapper
 *
 * @package Acamar\Model\Mapper
 */
class XmlMapper extends ArrayMapper
{
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


        return $this->populateFromArray([$data->getName() => json_decode(json_encode($data))], $map, $object);
    }

    /**
     * Creates an object using data from an array (which was converted from XML)
     *
     * @param array $data
     * @param string $map
     * @param EntityInterface|null $object
     * @return EntityInterface
     */
    protected function populateFromArray(array $data, $map = "default", EntityInterface $object = null)
    {
        echo 1;

        return $object;
    }
}