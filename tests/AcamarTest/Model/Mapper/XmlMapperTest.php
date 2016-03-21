<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTests\Model\Mapper;

use Acamar\Model\Mapper\XmlMapper;
use TestHelpers\AbstractTest;
use TestHelpers\Model\Mapper\XmlMapCollection;

/**
 * Class XmlMapperTest
 *
 * @package AcamarTests\Model\Mapper
 */
class XmlMapperTest extends AbstractTest
{
    public function testCanMapXml()
    {
        $mapper = new XmlMapper(new XmlMapCollection());
        $object = $mapper->populate($this->getResourceContents("complexXml.xml"), "catalog");


    }
}