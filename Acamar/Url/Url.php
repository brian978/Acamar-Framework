<?php
/**
 * Acamar Framework
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Url;

use Acamar\Validator\Url as UrlValidator;

/**
 * Class Url
 *
 * @package Acamar\Url
 */
class Url implements UrlInterface
{

    /**
     *
     * @var array
     */
    protected $options = array(
        'site_root' => '',
        'site_root_ssl' => '',
        'controller' => 'controller',
        'action' => 'action',
        'index_page' => 'index',
        'persistent_params' => array(),
        'rewrite' => false,
        'use_get_array' => false,
        'require_ssl' => false,
        'auto_initialize' => true,
    );

    /**
     * Current url
     *
     * @var string
     */
    protected $url;

    /**
     * Flag that determins if SSL should be used or not
     *
     * @var boolean
     */
    protected $useSsl = false;

    /**
     * Flag used to trigger SSL usage only once
     *
     * @var boolean
     */
    protected $tmp_ssl = false;

    /**
     * Array of params that the object will automatically load
     *
     * @var array
     */
    protected $persistent_params = array();

    /**
     * URL params (holds the params found in the URL
     *
     * @var array
     */
    private $params = array();

    /**
     * Rewrite active or not
     *
     * @var boolean
     */
    protected $rewrite;

    /**
     * @var \Acamar\Validator\Url
     */
    protected $urlValidator;

    /**
     * Class constructor. It also validates the URL
     *
     * @param array $options
     * @return \Acamar\Url\Url
     */
    public function __construct(array $options = array())
    {
        // ==== Replacing options with custom ones ==== //
        if (count($options) > 0) {
            $this->options = array_replace($this->options, $options);
        }

        // Checking if we should auto initialize the object
        if ($this->options['auto_initialize']) {
            $this->init();
        }
    }

    /**
     * Initializes the class
     *
     * @param void
     * @return void
     * @throws Exception\RuntimeException
     */
    public function init()
    {
        $this->urlValidator = new UrlValidator();

        if ($this->urlValidator->isValid($this->options['site_root']) === false) {
            throw new Exception\RuntimeException('Invalid site root URL.');
        } else {
            // ==== Changing to SSL if requested ==== //
            if ($this->options['require_ssl'] === true) {
                if ($this->urlValidator->isValid($this->options['site_root_ssl']) === false) {
                    throw new Exception\RuntimeException('Invalid SSL site root URL.');
                }

                $this->useSsl = true;
            }

            // ==== Setting rewrite property ==== //
            $this->rewrite = $this->options['rewrite'];

            // ==== Getting URL ==== //
            $this->url = self::getFullURL();

            // ==== Correcting the site roots so we don't have issues with the URL generation ==== //
            if (strlen($this->options['site_root']) > (strrpos($this->options['site_root'], '/') + 1)) {
                $this->options['site_root'] .= '/';
            }

            // ==== Correcting the SSL site root so we don't have issues with the URL generation ==== //
            if (!empty($this->options['site_root_ssl'])
                && strlen($this->options['site_root_ssl']) > (strrpos($this->options['site_root_ssl'], '/') + 1)
            ) {
                $this->options['site_root_ssl'] .= '/';
            }

            // ==== Correcting the detected URL ==== //
            if ($this->rewrite
                && strlen($this->url) > (strrpos($this->url, '/') + 1)
                && strpos($this->url, '?' . $this->options['controller'] . '=') === false
            ) {
                $this->url .= '/';
            }

            // Loading the params in $_GET
            $this->loadGetParams();

            // ==== Getting the URL data from GET or by splitting the URL ==== //
            $this->getURLData();

            // ==== Initializing the default params ==== //
            $this->initParams();
        }
    }

    /**
     *
     * Returns the current url (everything in the URL bar)
     *
     * @param void
     * @return string
     */
    public static function getFullURL()
    {
        $protocol    = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $domain      = $_SERVER['SERVER_NAME'];
        $request_uri = $_SERVER['REQUEST_URI'];

        return $protocol . $domain . $request_uri;
    }

    /**
     * Reverses the effect of parse_url
     *
     * @param array $comps
     * @return string
     */
    public static function deparseUrl($comps)
    {
        // Building the URL from the components
        $url = (isset($comps['scheme']) ? $comps['scheme'] : '') . '://' . // Protocol
            (isset($comps['host']) ? $comps['host'] : '') . // Host
            (isset($comps['port']) ? ':' . $comps['port'] : '') . // Port
            (isset($comps['path']) ? $comps['path'] : '') . // Path
            (isset($comps['query']) ? '?' . $comps['query'] : '') . // Query string
            (isset($comps['fragment']) ? $comps['fragment'] : ''); // Anchor

        // Returning the URL
        return $url;
    }

    /**
     * Gets the site root
     *
     * @param void
     * @return string
     */
    protected function getSiteRoot()
    {
        // Default site root
        $site_root = $this->options['site_root'];

        // Parsing the URL
        $parsed_url = parse_url($this->url);

        // Checking if the local site root should be modified
        if ($parsed_url['scheme'] == 'https') {
            $site_root = $this->options['site_root_ssl'];
        }

        // Returning the site root
        return $site_root;
    }

    /**
     * Loads the GET params
     *
     * @param void
     * @return void
     */
    protected function loadGetParams()
    {
        // Going through the elements in the $_GET array
        foreach ($_GET as $name => $value) {
            $this->setParam($name, rawurldecode(trim($value)));
        }

        // Resseting the $_GET array if we should not use it
        if ($this->options['use_get_array'] === false) {
            $_GET = array();
        }
    }

    /**
     * Retrieves data from the URL string
     *
     * @return void
     */
    protected function getURLData()
    {
        // ==== Setting some default values ==== //
        if ($this->getParam($this->options['controller']) === null) {
            $this->setParam($this->options['controller'], $this->options['index_page']);
        }

        // ==== Processing the URL only if it's not the site root ==== //
        if ($this->getSiteRoot() != $this->url) {
            // ==== Check variable to see if site root was found ==== //
            $found_site_root = 0;

            // ==== Creating a local site root copy to be able to handle the decoding of the URL ==== //
            $site_root = $this->getSiteRoot();

            // ==== Removing the site root from the URL ==== //
            $data = str_replace($site_root, '', $this->url, $found_site_root);

            // ==== Checking if something was replaced ==== //
            if ($found_site_root != 0) {
                // ==== Checking if the data (query string) contains the question mark sign ==== //
                if (strpos(trim($data), '?') !== 0) {
                    // ==== Breaking the URL into pieces ==== //
                    $data = explode('/', $data);

                    // ==== Removing the last piece of the array (if it's empty) ==== //
                    if (trim($data[count($data) - 1]) == '') {
                        array_pop($data);
                    }
                }

                // ==== Checking if there is any data to process ==== //
                if (is_array($data) && count($data) > 0) {
                    ////////////////////////////////////////////////////////////////
                    //    PROCESSING THE URL - REWRITE ENABLED/FOUND
                    ///////////////////////////////////////////////////////////////
                    // ==== Getting the controller ==== //
                    $this->setParam($this->options['controller'], $data[0]);

                    // ==== Getting the method ==== //
                    $this->setParam($this->options['action'], isset($data[1]) ? $data[1] : 'index');

                    // Data count
                    $count = count($data);

                    // ==== The data should contain an even number of elements ==== //
                    if ($count % 2 == 0) {
                        // ==== Going through the names and building the URL params array ==== //
                        for ($i = 2; $i < $count; $i++) {
                            // Adding the parameter data to the URL params array
                            if ($i % 2 == 0) {
                                $this->setParam($data[$i], $data[$i + 1]);
                            }
                        }
                    }

                    // ==== Merging the $_GET array with the $get array ==== //
                    if ($this->options['use_get_array'] === true) {
                        $_GET = array_merge($_GET, $this->params);
                    }
                }
            }
        }
    }

    /**
     * Initializes the parameters that must be passed along in
     * the URL, with the values found in the URL
     *
     * @param void
     * @return void
     */
    protected function initParams()
    {
        // ==== Checking if the get params option has some info in it ==== //
        if (count($this->options['persistent_params']) > 0) {
            // ==== Going through the $_GET params ==== //
            foreach ($this->options['persistent_params'] as $name) {
                // ==== Checking if the parameter exists ==== //
                if ($this->getParam($name) !== null) {
                    // ==== Trimming down the param ==== //
                    $value = $this->getParam($name);

                    // ==== Adding parameter to the class parameters ==== //
                    if (!empty($value)) {
                        $this->persistent_params[$name] = $value;
                    }
                }
            }
        }
    }

    /**
     * Gets a parameter from the URL
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        // Checking if the parameter exists
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        // Returning null if the parameter does not exist
        return $default;
    }

    /**
     * Sets a parameter
     *
     * @param string $name
     * @param mixed $value
     * @return object
     */
    public function setParam($name, $value)
    {
        // Setting the parameters value
        if (!empty($value)) {
            $this->params[$name] = rawurlencode(trim($value));
        }

        // Returning the current object
        return $this;
    }

    /**
     * Retrieves all the URL paramters
     *
     * @param void
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Retrieves the current page
     *
     * @param void
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->getParam($this->options['controller']);
    }

    /**
     * Replaces the values in the first array with ones from the
     * second one (similar to array_merge) but then appends the
     * remaining values from the first to the end of the second
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected static function arrayAppend($array1, $array2)
    {
        foreach ($array1 as $idx => $value) {
            if (isset($array2[$idx])) {
                $array1[$idx] = $array2[$idx];

                unset($array2[$idx]);
            }
        }

        $array = array_merge($array1, $array2);

        return $array;
    }

    /**
     * Changes the site root to the SSL one
     *
     * @throws Exception\RuntimeException
     * @return object
     */
    public function enableSSL()
    {
        // ==== Checking if the SSL site root is even set ==== //
        if ($this->urlValidator->isValid($this->options['site_root_ssl']) === true) {
            $this->useSsl = true;
        } else {
            // ==== Triggering an error ==== //
            throw new Exception\RuntimeException('Invalid SSL site root URL.');
        }

        return $this;
    }

    /**
     * Changes the site root to the non-SSL one
     *
     * @return object
     */
    public function disableSSL()
    {
        $this->useSsl = false;

        return $this;
    }

    /**
     * Used to trigger the temporary SSL (when you want SSL for a single link)
     *
     * @throws Exception\RuntimeException
     * @return object
     */
    public function ssl()
    {
        // ==== Checking if the SSL site root is even set ==== //
        if ($this->urlValidator->isValid($this->options['site_root_ssl']) === true) {
            $this->tmp_ssl = true;
        } else {
            // ==== Triggering an error ==== //
            throw new Exception\RuntimeException('Invalid SSL site root URL.');
        }

        return $this;
    }

    /**
     * Called when a call is made to the class like it's a function
     *
     * @param string $controller Page to link to
     * @param array $params
     * @param boolean $merge_get
     * @return string
     */
    public function __invoke(
        $controller = '',
        array $params = array(),
        $merge_get = false
    ) {
        return $this->get($controller, $params, $merge_get);
    }

    /**
     * Builds the URL using the provided params
     *
     * @param string $controller Page to link to
     * @param array $params
     * @param boolean $merge_get
     * @return string
     */
    public function get(
        $controller,
        array $params = array(),
        $merge_get = false
    ) {
        // Default site root to use
        $url = $this->options['site_root'];

        // ==== Getting the SSL site root if required ==== //
        if ($this->useSsl === true || $this->tmp_ssl === true) {
            $url = $this->options['site_root_ssl'];

            // Disabing the temporary SSL
            $this->tmp_ssl = false;
        }

        // Merging the params if required
        if ($merge_get === true) {
            // This should now contain all the parameters (first ones should be the given ones)
            $get_params = array_replace($params, $this->getParams());

            // Now that we have all the params in order we override the get params with the given params
            $params = array_replace($get_params, $params);
        }

        // Now we need to add the persistent params to the given ones (persistent should be the last ones)
        $persistent_params = array_replace($params, $this->persistent_params);

        // Now that we have all the params in order we override the persistent params with the given params
        $params = array_replace($persistent_params, $params);

        // If the params count is higher then 1 we need to make sure we have an action
        if (count($params) >= 1) {
            // Method param
            if (empty($params[$this->options['action']])) {
                $params[$this->options['action']] = 'index';
            }
        }

        // The characters that join the parameters (default)
        $glue1 = '&';
        $glue2 = '=';

        // ==== Processing the data to generate the URL ==== //
        if ($this->rewrite) {
            ////////////////////////////////////////////////////////////////
            //    REWRITE ENABLED
            ///////////////////////////////////////////////////////////////
            // The characters that join the parameters
            $glue1 = $glue2 = '/';

            // ==== The URL has the dash already ==== //
            $url .= $controller;

            // ==== Checking for the rest of the params ==== //
            if (!empty($params[$this->options['action']])) {
                $url .= $glue1 . $params[$this->options['action']];

                // Removing the action
                unset($params[$this->options['action']]);
            }
        } elseif (!empty($controller)) {
            ////////////////////////////////////////////////////////////////
            //    REWRITE DISABLED
            ///////////////////////////////////////////////////////////////
            // ==== Building the first part of the URL ==== //
            $url .= '?' . $this->options['controller'] . $glue2 . $controller;

            // ==== Checking for the rest of the params ==== //
            if (!empty($params[$this->options['action']])) {
                $url .= $glue1;
                $url .= $this->options['action'];
                $url .= $glue2;
                $url .= $params[$this->options['action']];

                // Removing the action
                unset($params[$this->options['action']]);
            }
        }

        // Removing the controller from the params if it has been set
        if (!empty($params[$this->options['controller']])) {
            unset($params[$this->options['controller']]);
        }

        // ==== Going through the params and building the URL ==== //
        foreach ($params as $name => $value) {
            // ==== Adding the parameter to the URL ==== //
            if (trim($value) != '') {
                $url .= $glue1 . $name . $glue2 . $value;
            }
        }

        // ==== Returning result ==== //
        return $url;
    }
}
