<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2016
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace AcamarTests\Model\Mapper;

use TestHelpers\AbstractTest;

/**
 * Class XmlMapperTest
 *
 * @package AcamarTests\Model\Mapper
 */
class XmlMapperTest extends AbstractTest
{
    public function testCanMapXml()
    {
        $xmlObject = new \SimpleXMLElement($this->getResourceContents("complexXml.xml"));

        $array = json_decode(json_encode($xmlObject), true);
        $array = array($xmlObject->getName() => $array);

        echo 1;
    }
}