<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc\Router;

use Acamar\Http\Request;

/**
 * Class Route
 *
 * @package Acamar\Mvc\Router
 */
class Route
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $pattern = '';

    /**
     * @var array
     */
    protected $defaults = [
        'module' => 'Application',
        'controller' => 'index',
        'action' => 'index'
    ];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $settableOptions = [
        'acceptedHttpMethods',
    ];

    /**
     * This string will contain the regex that must be applied on a request URI
     *
     * @var string
     */
    protected $regex = '';

    /**
     * Will contain the route parts
     *
     * @var array
     */
    protected $parts = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $paramNames = [];

    /**
     * List of accepted HTTP methods
     *
     * @var array
     */
    protected $acceptedHttpMethods = array(
        Request::METHOD_GET,
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_DELETE,
        Request::METHOD_OPTIONS
    );

    /**
     * Factory method for the route object
     *
     * @param array $config
     * @return Route
     * @throws \InvalidArgumentException
     */
    public static function factory(array $config)
    {
        if (!isset($config['name']) || !is_string($config['name']) || empty($config['name'])) {
            throw new \InvalidArgumentException('The route name must be a string');
        }

        if (!isset($config['pattern']) || !is_string($config['pattern']) || empty($config['pattern'])) {
            throw new \InvalidArgumentException('The route pattern must be a string');
        }

        if (!isset($config['defaults']) || !is_array($config['defaults'])) {
            $config['defaults'] = [];
        }

        if (!isset($config['options']) || !is_array($config['options'])) {
            $config['options'] = [];
        }


        return new self($config['name'], $config['pattern'], $config['defaults'], $config['options']);
    }

    /**
     * Creates a route object
     *
     * @param string $name
     * @param $pattern
     * @param array $defaults
     * @param array $options
     */
    public function __construct($name, $pattern, array $defaults = array(), array $options = array())
    {
        $this->setName($name);
        $this->setPattern($pattern);
        $this->setDefaults($defaults);
        $this->setOptions($options);
    }

    /**
     * Sets the route name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Returns the route name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set route pattern
     *
     * @param string $pattern
     * @return $this|void
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        // Resetting the regex because it will not match the new pattern otherwise
        $this->regex = '';

        return $this;
    }

    /**
     * Get route pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param array $defaults
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setDefaults($defaults)
    {
        $defaults = array_merge($this->defaults, $defaults);

        // Validating the defaults
        foreach (['module', 'controller', 'action'] as $key) {
            if (!isset($defaults[$key]) || empty($defaults[$key]) || !is_string($defaults[$key])) {
                throw new \InvalidArgumentException(
                    'The default `' . $key . '` for the `' . $this->name . '` route is invalid'
                );
            }
        }

        $this->defaults = $defaults;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        // Setting the properties that can be set from the options array
        foreach ($this->settableOptions as $option) {
            if (isset($options[$option])) {
                $this->{$option} = $options[$option];
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $acceptedHttpMethods
     * @return $this
     */
    public function setAcceptedHttpMethods($acceptedHttpMethods)
    {
        $this->acceptedHttpMethods = $acceptedHttpMethods;

        return $this;
    }

    /**
     * @return array
     */
    public function getAcceptedHttpMethods()
    {
        return $this->acceptedHttpMethods;
    }

    /**
     * Checks if a HTTP method is supported
     *
     * @param string $method
     * @return bool
     */
    public function acceptsHttpMethod($method)
    {
        return in_array(strtoupper($method), $this->acceptedHttpMethods);
    }

    /**
     * Checks if a HTTP request is accepted (based on it's method)
     *
     * @param Request $request
     * @return bool
     */
    public function acceptsHttpRequest(Request $request)
    {
        return $this->acceptsHttpMethod($request->getMethod());
    }

    /**
     * Extract the route parameters
     *
     * @param string $pattern
     * @throws \RuntimeException
     * @return $this
     */
    protected function parseRoute($pattern)
    {
        $currentPos = 0;
        $length     = strlen($pattern);
        $parts      = [];

        while ($currentPos < $length) {
            preg_match('#(?P<literal>[\w-_\/]*)(?P<token>[(:)])#', $pattern, $matches, 0, $currentPos);

            // Literal
            if (!empty($matches['literal'])) {
                $parts[] = array(
                    'type' => 'literal',
                    'part' => $matches['literal']
                );

                $currentPos += strlen($matches[0]);
            }

            switch ($matches['token']) {
                // Parameter
                case ':':
                    preg_match('#(?P<param>[\w]*)([^\w]?)#', $pattern, $matches, 0, $currentPos);
                    if (empty($matches['param'])) {
                        throw new \RuntimeException('Empty parameter found');
                    }

                    $parts[] = array(
                        'type' => 'parameter',
                        'part' => $matches['param']
                    );

                    // We need to keep track of the parameter names as well
                    // so we can extract them after a match
                    $this->paramNames[] = $matches['param'];

                    $currentPos += strlen($matches['param']);
                    break;

                // Begin optional parameter
                case '(':
                    // We need the optional array so we can reference part of it
                    // without counting the existing parts
                    $opt = array(
                        'type' => 'optional',
                        'part' => array(
                            'ref' => &$parts // We keep a reference to know where to get back
                        )
                    );

                    // Now we swap the arrays
                    $parts[] = $opt;
                    $parts   = & $opt['part'];

                    // Free some memory
                    unset($opt);


                    // We need to get past the parenthesis or else something will break
                    $currentPos += 1;
                    break;

                // End optional parameter
                case ')':
                    // We need this to make the array swap
                    $parentParts = & $parts['ref'];

                    // The first parent array of parts won't have a reference to another array
                    // so we need to take that in account
                    $count = 1;
                    if (isset($parentParts['ref'])) {
                        $count = 2;
                    }

                    // Don't need this anymore
                    unset($parts['ref']);

                    // Copying what we have so far into the parents
                    $parentParts[count($parentParts) - $count] = array(
                        'type' => 'optional',
                        'part' => $parts
                    );

                    // Making the swap so we can go up one level
                    $parts = & $parentParts;

                    // Free some memory
                    unset($parentParts, $count);

                    // We need to get past the parenthesis or else something will break
                    $currentPos += 1;
                    break;
            }
        }

        $this->parts = $parts;

        return $this;
    }

    /**
     * Pre-computes the regular expression, based on the route pattern, that will be used when matching and URI
     *
     * @return $this
     */
    protected function createRegex()
    {
        $this->regex = '#\G' . $this->assembleRegexParts($this->parts) . '((?P<wildcard>[\w\/-_]+))?#';

        return $this;
    }

    /**
     * @param array $parts
     * @return string
     */
    protected function assembleRegexParts(array $parts)
    {
        $string = '';

        foreach ($parts as $part) {
            switch ($part['type']) {
                case 'literal':
                    $string .= str_replace('/', '\/', $part['part']);
                    break;

                case 'optional':
                    $string .= '(' . $this->assembleRegexParts($part['part']) . ')?';
                    break;

                case 'parameter':
                    $string .= '(?P<' . $part['part'] . '>[\w-_]+)';
                    break;
            }
        }

        return $string;
    }

    /**
     * Used to check if the route matched the given URL
     *
     * @param  string $requestUri A Request URI
     * @return bool
     */
    public function matches($requestUri)
    {
        // Only calculate the regex on demand because it might not even get to this if another route matches first
        if (empty($this->regex)) {
            $this->parseRoute($this->pattern)->createRegex();
        }

        // Trying to match the URI
        if (!preg_match($this->regex, $requestUri, $paramValues)) {
            return false;
        }

        if (isset($paramValues['wildcard']) && !empty($paramValues['wildcard'])) {
            $paramValues = $this->validateWildcard($paramValues);
        }

        foreach ($this->paramNames as $name) {
            if (isset($paramValues[$name]) && !empty($paramValues[$name])) {
                $this->params[$name] = urldecode($paramValues[$name]);
            } elseif (isset($this->defaults[$name])) {
                $this->params[$name] = $this->defaults[$name];
            }
        }

        return true;
    }

    /**
     * Returns pieces for the regular expression that must be used to match the URL
     *
     * @param  array $m URL parameters
     * @return string Regular expression for URL parameter
     */
    protected function matchesCallback($m)
    {
        $name  = $m[2];
        $slash = $m[1];

        $this->paramNames[] = $name;

        // Escaping some chars
        if (!empty($slash)) {
            $slash = '\\' . $slash;
        }

        // This is basically how "/:controller" translates in regex
        $baseRegEx = $slash . '(?P<' . $name . '>[^\/]+)';

        // The final regex depends on optional params
        if (strpos($m[0], '(/') === 0) {
            return '(' . $baseRegEx . ')?';
        } elseif (strpos($m[0], '(') === 0) {
            return $baseRegEx . '?';
        }

        return $baseRegEx;
    }

    /**
     * Parses the wildcard parameters (if they are valid)
     *
     * @param array $routeMatches
     * @return array
     */
    protected function validateWildcard(array $routeMatches)
    {
        $wildcard       = ltrim($routeMatches['wildcard'], '/');
        $wildcardPieces = explode('/', $wildcard);
        $piecesCount    = count($wildcardPieces);
        if ($piecesCount % 2 === 0) {
            for ($i = 0; $i < $piecesCount - 1; $i += 2) {
                $this->paramNames[]                = $wildcardPieces[$i];
                $routeMatches[$wildcardPieces[$i]] = $wildcardPieces[$i + 1];
            }
        }

        return $routeMatches;
    }

    /**
     * Creates an URL using the route information
     *
     * @param array $params
     * @param Route $currentRoute
     * @return string
     */
    public function assemble(array $params, Route $currentRoute)
    {
        // Getting the params from the route so we can replace them
        preg_match_all('#:([\w]+)#', $this->pattern, $matchedParams);

        // We need the group matches
        $routeParams = $matchedParams[1];

        // First we get rid of all the parenthesis
        $result = str_replace(array('(', ')'), '', $this->pattern);

        // Building the route using the provided params
        foreach ($routeParams as $routeParam) {
            if (isset($params[$routeParam])) {
                $value = $params[$routeParam];
                unset($params[$routeParam]);
            } else {
                $value = $currentRoute->getParam($routeParam);
            }

            $result = str_replace(':' . $routeParam, $value, $result);
        }

        // Attaching the rest of the params
        foreach ($params as $key => $value) {
            $result .= '/' . $key . '/' . $value;
        }

        return $result;
    }

    /**
     * Set route parameter value
     *
     * @param string $name
     * @param string|number $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        if (is_string($name) && (is_string($value) || is_numeric($value))) {
            $this->params[$name] = $value;
        }

        return $this;
    }

    /**
     * Get route parameter value
     *
     * @param string $name
     * @param null $default
     * @return string|mixed
     */
    public function getParam($name, $default = null)
    {
        if (is_string($name)) {
            if (isset($this->params[$name])) {
                return $this->params[$name];
            }

            if (isset($this->defaults[$name])) {
                return $this->defaults[$name];
            }
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->getParam('module', 'Application');
    }

    /**
     *
     * @return string
     */
    public function getControllerName()
    {
        return strtolower($this->getParam('controller', ''));
    }

    /**
     *
     * @return string
     */
    public function getActionName()
    {
        return strtolower($this->getParam('action', 'index'));
    }

    /**
     *
     * @return string
     */
    public function getControllerClass()
    {
        if (isset($this->params['controller'])) {
            $controllerName = ucfirst($this->params['controller']);
        } else {
            $controllerName = ucfirst($this->defaults['controller']);
        }

        return $this->defaults['module'] . '\\Controller\\' . $controllerName . 'Controller';
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        $actionName = 'index';

        if (isset($this->params['action'])) {
            $actionName = lcfirst($this->params['action']);
        } else if (isset($this->defaults['action'])) {
            $actionName = lcfirst($this->defaults['action']);
        }

        return $actionName . 'Action';
    }
}
