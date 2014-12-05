<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Loader;

// Loading the dependencies
require_once 'LoaderInterface.php';

/**
 * Class PSR4Autoloader
 *
 * @package Acamar\Loader
 */
class PSR4Autoloader implements LoaderInterface
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
            if (is_string($path)) {
                $this->registerNamespace($namespace, $path);
            } else if (is_array($path)) {
                foreach ($path as $value) {
                    $this->registerNamespace($namespace, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Registers a given namespace
     *
     * @param string $namespace
     * @param string|array $path
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function registerNamespace($namespace, $path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('The path must be a string');
        }

        $path = realpath($path);
        if (!empty($path)) {
            if (!isset($this->namespaces[$namespace])) {
                $this->namespaces[$namespace] = [];
            }

            $this->namespaces[$namespace][] = $path;
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
            foreach ($this->namespaces[$namespace] as $path) {
                $file = $path;
                $file .= DIRECTORY_SEPARATOR;
                $file .= str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

                // Checking if a file exists for the requested class to avoid errors/warnings
                if (is_file($file)) {
                    include $file;
                    break;
                }
            }
        }
    }
}
