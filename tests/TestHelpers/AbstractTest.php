<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace TestHelpers;

use PHPUnit_Framework_TestCase;

/**
 * Class AbstractTest
 *
 * @package TestHelpers
 */
class AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * AbstractTest constructor.
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Returns the file path to a resources
     *
     * @param string $filename
     * @return string
     */
    protected function getResourcePath($filename)
    {
        $resourcesFolder = getcwd() . DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR;
        $path = realpath($resourcesFolder . $filename);

        if (empty($path)) {
            throw new \RuntimeException("The resource '{$filename}' could not be found in '{$resourcesFolder}'");
        }

        return $path;
    }

    /**
     * Returns the content of a resource file
     *
     * @param $filename
     * @return string
     */
    protected function getResourceContents($filename)
    {
        return file_get_contents($this->getResourcePath($filename));
    }
}
