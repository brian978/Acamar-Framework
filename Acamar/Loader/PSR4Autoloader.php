<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
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
    protected $namespaces = [];

    /**
     * @var array
     */
    protected $resolved = [];

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
    public function loadClass($class)
    {
        if ($this->resolveNamespace($class)) {
            $namespace = & $this->resolved[$class]['ns'];
            $class     = & $this->resolved[$class]['class'];

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

    /**
     * Extracts the registered namespace from the class name
     *
     * @param string $class
     * @return boolean
     */
    protected function resolveNamespace($class)
    {
        if (isset($this->resolved[$class])) {
            return $this->resolved[$class]['ns'];
        }

        $resolved  = false;
        $pieces    = explode('\\', $class);
        $namespace = '';

        do {
            $namespace .= array_shift($pieces);
            if (isset($this->namespaces[$namespace])) {
                $resolved = true;
            } else {
                $namespace .= '\\';
            }
        } while (!$resolved && !empty($pieces));

        // Caching our results
        $this->resolved[$class] = [
            'ns' => $namespace,
            'class' => str_replace($namespace . '\\', '', $class)
        ];

        return $resolved;
    }
}
