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
    public static function fromArray(array $config)
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
     * Used to check if the route matched the given URL
     *
     * @param  string $resourceUri A Request URI
     * @return bool
     */
    public function matches($resourceUri)
    {
        // Convert URL params into regex patterns
        $patternAsRegex = preg_replace_callback(
            '#\(?(\/)?:([\w]+)\)?#',
            array($this, 'matchesCallback'),
            (string) $this->pattern
        );

        $regex = '#^' . $patternAsRegex . '(?P<wildcard>(\/[^\/]+)*)$#';

        // Trying to match the URI
        if (!preg_match($regex, $resourceUri, $paramValues)) {
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
            $slash = addslashes($slash);
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
