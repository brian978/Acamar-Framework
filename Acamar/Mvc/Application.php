<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc;

use Acamar\Config\Config;
use Acamar\Event\EventManager;
use Acamar\Http\Cgi\Request;
use Acamar\Http\Response;
use Acamar\Loader\LoaderInterface;
use Acamar\Loader\PSR0Autoloader;
use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\Router\Route;
use Acamar\Mvc\Router\Router;
use Acamar\Mvc\View\Renderer\Strategy\RenderingStrategyFactory;
use Acamar\Mvc\View\Renderer\ViewRenderer;

class Application implements ApplicationInterface
{
    const ENV_PHPUNIT     = 'phpunit';
    const ENV_DEVELOPMENT = 'development';
    const ENV_STAGING     = 'staging';
    const ENV_PRODUCTION  = 'production';

    /**
     * @var PSR0Autoloader
     */
    protected $autoloader = null;

    /**
     * @var Config
     */
    protected $config = null;

    /**
     * @var string
     */
    protected $env = "";

    /**
     * @var Router
     */
    protected $router = null;

    /**
     * @var EventManager
     */
    protected $eventManager = null;

    /**
     * @var Dispatcher
     */
    protected $dispatcher = null;

    /**
     * Constructs the Application object
     *
     * @param string $env
     * @param \Acamar\Loader\LoaderInterface $autoloader
     */
    public function __construct($env = self::ENV_PRODUCTION, LoaderInterface $autoloader)
    {
        $this->env          = $env;
        $this->autoloader   = $autoloader;
        $this->eventManager = new EventManager();
        $this->router       = new Router($this->eventManager);
        $this->dispatcher   = new Dispatcher($this->eventManager);
        $this->config       = new Config();

        // registering the error handler
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * The method allows the controller (or any other object that gets the event object) to access the config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * The method allows the controller (or any other object that gets the event object) to access the router
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * The method allows the controller (or any other object that gets the event object) to access the event manager
     *
     * @return \Acamar\Event\EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     *
     * @return $this
     */
    protected function registerNamespaces()
    {
        $namespaces  = [];
        $modulesPath = $this->config['modulesPath'];

        // The autoloader will call the "realpath" function on all the provided paths
        // so we don't need to this here as well
        foreach ($this->config['modules'] as $module => $cfgFiles) {
            $namespaces[$module] = $modulesPath . '/' . $module . '/src/main';

            // When we are in a test environment we will also create the paths for the test folders
            if ($this->env === static::ENV_PHPUNIT) {
                $testDir = $modulesPath . '/' . $module . '/src/test';
                if (is_dir($testDir)) {
                    $namespaces[$module . 'Test'] = $testDir;
                }
            }
        }

        $this->autoloader->registerNamespaces($namespaces);

        return $this;
    }

    /**
     * This is called directly from the index.php file before the Application::run() method
     *
     * @throws \RuntimeException
     * @return $this
     */
    protected function loadConfig()
    {
        $configPath = realpath('config/application.config.php');
        if (!is_file($configPath)) {
            throw new \RuntimeException('The main "application.config.php" was not found in the "config" folder');
        }

        $configCached = false;

        // We get the application configuration here so we can check the config caching flag before we
        // add the config into the Config object
        $appConfig = require $configPath;

        // Checking if the configuration cache is enabled
        $isConfigCacheEnabled = false;
        if (isset($appConfig['configCache']) && !empty($appConfig['configCache']['filePath'])) {
            $isConfigCacheEnabled = (bool) $appConfig['configCache']['enabled'];
        }

        // Getting the cached version if it exists and setting a flag
        $appCachedConfigFilePath = $appConfig['configCache']['filePath'];
        if ($isConfigCacheEnabled && file_exists($appCachedConfigFilePath)) {
            $cachedConfig = require $appCachedConfigFilePath;

            if (time() - $cachedConfig['cacheCreateTime'] < $appConfig['configCache']['lifetime']) {
                $configCached = true;
                $appConfig    = & $cachedConfig;
            }
        }

        // For now we have a single global file
        $this->config->add($appConfig);

        // Loading the configurations for the modules
        if ($configCached === false) {
            $this->loadModuleConfigs();
        }

        // Caching the configuration if required
        if ($isConfigCacheEnabled && !$configCached) {
            $this->cacheConfig();
        }

        return $this;
    }

    /**
     *
     * @return $this
     */
    protected function loadModuleConfigs()
    {
        $modulesPath        = $this->config['modulesPath'];
        $defaultConfigFiles = $this->config['modulesConfigs'];

        foreach ($this->config['modules'] as $module => $setup) {
            $configFiles = null;
            if (isset($setup['configs'])) {
                $configFiles = & $setup['configs'];
            }

            if (!is_array($configFiles)) {
                $configFiles = & $defaultConfigFiles;
            } elseif (empty($configFiles)) {
                continue;
            }

            // Merging the configuration files
            $moduleConfigPath = realpath($modulesPath . '/' . $module . '/resources/config');
            foreach ($configFiles as $cfgFile) {
                $this->config->add(require $moduleConfigPath . DIRECTORY_SEPARATOR . $cfgFile);
            }
        }

        return $this;
    }

    /**
     * The method cache the current configuration
     *
     * @return $this
     */
    protected function cacheConfig()
    {
        // We need to add the cache create time so we can determine when the cache should expire
        $config                    = $this->config->getArrayCopy();
        $config['cacheCreateTime'] = time();

        // Saving the cache file
        $data = "<?php \n";
        $data .= "return ";
        $data .= var_export($config, 1);
        $data .= ";";

        file_put_contents($this->config['configCache']['filePath'], $data);

        return $this;
    }

    /**
     * Creates the router object and initializes the routes
     *
     * @return $this
     */
    protected function loadRoutes()
    {
        // If we don't have any routes we don't add anything to the router
        if (isset($this->config['routes']) && count($this->config['routes']) > 0) {
            foreach ($this->config['routes'] as $name => $info) {
                /** @var $routeClass \Acamar\Mvc\Router\Route */
                $routeClass = $this->router->getRouteClass();

                $this->router->addRoute($routeClass::factory($name, $info));
            }
        }

        return $this;
    }

    /**
     * @param Event\MvcEvent $event
     * @return $this
     */
    protected function runSetups(MvcEvent $event)
    {
        foreach ($this->config['modules'] as $module => $setup) {
            if (!isset($setup['runSetup']) || !$setup['runSetup']) {
                continue;
            }

            $class = $module . '\\' . 'Setup';

            // The setup will be run in the __construct() method of the class
            new $class($event);
        }

        return $this;
    }

    /**
     * Registers the namespaces, initializes the routing and dispatches the request
     *
     */
    public function run()
    {
        // Adding some default event handlers
        $this->eventManager->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'onBootstrap']);
        $this->eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError']);
        $this->eventManager->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender']);
        $this->eventManager->attach(MvcEvent::EVENT_RENDERED, [$this, 'onRenderComplete']);

        // Starting the event group
        $mainEvent = new MvcEvent(MvcEvent::EVENT_BOOTSTRAP, $this);
        $mainEvent->setRequest(new Request());
        $mainEvent->setResponse(new Response());

        $this->eventManager->trigger($mainEvent);
    }

    /**
     * Handles uncaught exceptions
     *
     * This is basically the default error handler
     *
     * @param \Exception $e
     */
    public function handleException(\Exception $e)
    {
        if ($this->dispatcher) {
            $route = $this->router->getRoute('error');
            if ($route instanceof Route === true) {
                /** @var $lastEvent MvcEvent */
                $lastEvent = $this->eventManager->getLastEvent();

                // Dispatch a request to the error controller with the exception
                $event = clone $lastEvent;
                $event->setName(MvcEvent::EVENT_DISPATCH);
                $event->setRoute($route);
                $event->setResponse(new Response());
                $event->setError($e);

                // We must stop the last event or else we will have some strange behaviors
                $lastEvent->stopPropagation(true);

                // We trigger the already created object so we can control what type of event object it is triggered
                $this->eventManager->trigger($event);
            } else {
                echo $e->getMessage();
            }
        } else {
            echo 'Something went very wrong.';
        }
    }

    /**
     * This is run on the "bootstrap" event
     *
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $this->loadConfig();
        $this->registerNamespaces();
        $this->loadRoutes();
        $this->runSetups($e);

        $this->eventManager->forward($e, MvcEvent::EVENT_ROUTE);
    }

    /**
     * This is run on the "dispatch.error" event
     *
     * @param MvcEvent $e
     * @throws \RuntimeException
     */
    public function onDispatchError(MvcEvent $e)
    {
        $route = $this->router->getRoute('error');
        if ($route instanceof Route === false) {
            throw new \RuntimeException('Page not found (and error page is not available)');
        }

        $e->setRoute($route);

        // We trigger the already created object so we can control what type of event object it is triggered
        $this->eventManager->forward($e, MvcEvent::EVENT_DISPATCH);
    }

    /**
     * This is run after the dispatch has been completed
     *
     * @param MvcEvent $e
     */
    public function onRender(MvcEvent $e)
    {
        // Preparing the view renderer
        $renderer = new ViewRenderer($e, $this->config);
        $renderer->setRenderingStrategy(RenderingStrategyFactory::factory($e));
        $renderer->render();

        $this->eventManager->forward($e, MvcEvent::EVENT_RENDERED);
    }

    /**
     * This is run after the dispatch has been completed
     *
     * @param \Acamar\Mvc\Event\MvcEvent $e
     */
    public function onRenderComplete(MvcEvent $e)
    {
        $response = $e->getResponse();
        if ($response instanceof Response) {
            $response->sendContent();
        }
    }
}
