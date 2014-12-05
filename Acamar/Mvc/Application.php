<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc;

use Acamar\Config\Config;
use Acamar\Event\EventManager;
use Acamar\Http\Request;
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
     * @var array
     */
    protected $configFileScopes = [];

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
     * @param array $configFileScopes
     */
    public function __construct($env = self::ENV_PRODUCTION, $configFileScopes = [])
    {
        $this->env          = $env;
        $this->eventManager = new EventManager();
        $this->router       = new Router($this->eventManager);
        $this->dispatcher   = new Dispatcher($this->eventManager);

        if (empty($configFileScopes)) {
            $this->configFileScopes[] = 'global';
            $this->configFileScopes[] = 'config';
            $this->configFileScopes[] = $env;
            $this->configFileScopes[] = 'local';
        } else {
            $this->configFileScopes = $configFileScopes;
        }

        // registering the error handler
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * Sets the autoloader object
     *
     * @param LoaderInterface $autoloader
     * @return $this
     */
    public function setAutoloader(LoaderInterface $autoloader)
    {
        $this->autoloader = $autoloader;

        return $this;
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
     *
     * @return $this
     */
    protected function registerNamespaces()
    {
        $namespaces  = [];
        $modulesPath = $this->config['modulesPath'];

        // Creating the paths for the namespaces that need to be registered
        // When we are in a test environment we will also create the paths for the test folders
        foreach ($this->config['modules'] as $module) {
            $namespaces[$module] = realpath($modulesPath . '/' . $module . '/src/main');
            if ($this->env === static::ENV_PHPUNIT) {
                $testDir = realpath($modulesPath . '/' . $module . '/src/test');
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
     * @return $this
     */
    protected function loadAppConfig()
    {
        // The config files array must have the order of the config file scopes
        $configFiles = [];
        foreach ($this->configFileScopes as $scope) {
            $configFiles[$scope] = [];
        }

        // Initializing the required vars
        $ds           = DIRECTORY_SEPARATOR;
        $files        = scandir('config');
        $scopePattern = implode('|', $this->configFileScopes);

        // Initializing the config
        $this->config = new Config();

        // Building the list of files to load
        foreach ($files as $file) {
            $matches = [];
            if (preg_match('^(.+)\.(' . $scopePattern . ').php^', $file, $matches) === 1 && isset($matches[2])) {
                $configFiles[$matches[2]][] = realpath('config' . $ds . $file);
            }
        }

        // Loading the files
        foreach ($configFiles as $paths) {
            foreach ($paths as $filePath) {
                $this->config->add(require $filePath);
            }
        }

        return $this;
    }

    /**
     *
     * @return $this
     */
    protected function loadModuleConfigs()
    {
        $modulesPath = $this->config['modulesPath'];
        foreach ($this->config['modules'] as $module) {
            $moduleConfig = realpath($modulesPath . '/' . $module . '/resources/config/module.config.php');
            $this->config->add(require $moduleConfig);
        }

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
                $info['name'] = $name;

                /** @var $routeClass \Acamar\Mvc\Router\Route */
                $routeClass = $this->router->getRouteClass();

                $this->router->addRoute($routeClass::factory($info));
            }
        }

        return $this;
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
     * This is run on the "bootstrap" event
     *
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $this->loadAppConfig();
        $this->registerNamespaces();
        $this->loadModuleConfigs();
        $this->loadRoutes();

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
