<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Loader;

/**
 * Interface LoaderInterface
 *
 * @package Acamar\Loader
 */
interface LoaderInterface
{
    /**
     * Registers the autoloader class as the __autoload() implementation
     *
     * @return void
     */
    public function register();

    /**
     * Registers a list of namespaces
     *
     * @param array $namespaces
     * @return $this
     */
    public function registerNamespaces(array $namespaces = []);

    /**
     * Registers a given namespace
     *
     * @param string $namespace
     * @param string $path
     * @return $this
     */
    public function registerNamespace($namespace, $path);

    /**
     * Loads a class based on it's name
     *
     * @param string $class
     * @return void
     */
    public function loadClass($class);
}
