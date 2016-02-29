<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Loader;

require_once 'LoaderInterface.php';

/**
 * Class PSR0Autoloader
 *
 * @package Acamar\Loader
 */
class PSR0Autoloader implements LoaderInterface
{
    /**
     * Namespaces
     *
     * @var array
     */
    protected $namespaces = [];

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
            $this->registered = spl_autoload_register([$this, 'loadClass']);
        }
    }

    /**
     * Registers a list of namespaces
     *
     * @param array $namespaces
     * @return $this
     */
    public function registerNamespaces(array $namespaces = [])
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
    public function loadClass($class)
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
