<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Loader;

/**
 * Class PSR0Autoloader
 *
 * @package Acamar\Loader
 */
class PSR0Autoloader
{
    /**
     * Namespaces
     *
     * @var array
     */
    protected $namespaces = array();

    /**
     * @var bool
     */
    protected $registered = false;

    /**
     * Registers the PSR0Autoloader class as the __autoload() implementation
     *
     */
    public function register()
    {
        if ($this->registered === false) {
            $this->registered = spl_autoload_register(array($this, 'loadClass'));
        }
    }

    /**
     * Registers a list of namespaces
     *
     * @param array $namespaces
     * @return $this
     */
    public function registerNamespaces(array $namespaces = array())
    {
        foreach ($namespaces as $namespace => $path) {
            $this->registerNamespace($namespace, $path);
        }

        return $this;
    }

    /**
     * Registers a given namespace
     *
     * @param string $namespace
     * @param string $path
     * @return $this
     */
    public function registerNamespace($namespace, $path)
    {
        $path = realpath($path);
        if (!empty($path)) {
            $this->namespaces[$namespace] = $path;
        }

        return $this;
    }

    /**
     * Loads a requested class
     *
     * @param string $class
     * @return void
     */
    protected function loadClass($class)
    {
        // Getting the namespace of the class
        $namespace = substr($class, 0, strpos($class, '\\'));

        if (isset($this->namespaces[$namespace])) {
            $file = $this->namespaces[$namespace];
            $file .= DIRECTORY_SEPARATOR;
            $file .= str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            // Checking if a file exists for the requested class to avoid errors/warnings
            if (is_file($file)) {
                include $file;
            }
        }
    }
}
