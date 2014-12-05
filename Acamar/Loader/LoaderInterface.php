<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
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
    public function registerNamespaces(array $namespaces = array());

    /**
     * Registers a given namespace
     *
     * @param string $namespace
     * @param string $path
     * @return $this
     */
    public function registerNamespace($namespace, $path);
}