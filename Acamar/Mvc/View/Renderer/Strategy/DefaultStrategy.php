<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc\View\Renderer\Strategy;

use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\View;

/**
 * Class DefaultStrategy
 *
 * @package Acamar\Mvc\View\Renderer
 */
class DefaultStrategy extends AbstractRenderingStrategy implements RenderingStrategyInterface
{
    /**
     * Constructs a rendering strategy object and configures the View
     *
     * @param MvcEvent $event
     */
    public function __construct(MvcEvent $event)
    {
        parent::__construct($event);

        $this->configureTheView();
    }

    /**
     * Configures the View object by setting the appropriate properties
     *
     * @return void
     */
    protected function configureTheView()
    {
        $config = $this->event->getTarget()->getConfig();
        $view = $this->event->getView();

        if ($view instanceof View) {
            // Configure the ViewHelperManager
            $viewHelperManager = $view->getViewHelperManager();
            $viewHelperManager->setConfig($config);
            $viewHelperManager->setEvent($this->event);

            // Configure the View with the template
            $route = $this->event->getRoute();

            if (isset($config['view']['paths'][$route->getModuleName()])) {
                $view->setTemplatesPath($config['view']['paths'][$route->getModuleName()]);
            }

            if ('' === $view->getLayoutTemplate()) {
                if(isset($config['view']['layout'][$route->getModuleName()])) {
                    $view->setLayoutTemplate($config['view']['layout'][$route->getModuleName()]);
                } else {
                    $view->setLayoutTemplate('layout/layout.phtml');
                }
            }

            if ('' === $view->getTemplate()) {
                $view->setTemplate($route->getControllerName() . DIRECTORY_SEPARATOR . $route->getActionName());
            }
        }
    }

    /**
     * Renders the View and returns the rendered content
     *
     * @return string
     */
    public function render()
    {
        $response = $this->event->getResponse();
        if (false === $response->getContentType()) {
            $response->setContentType('text/html');
        }

        $layoutTemplate = $this->view->getLayoutTemplate();
        if ('' === $layoutTemplate) {
            return $this->view->getContents();
        }

        // We will need a layout
        $layout = new View();
        $layout->setViewHelperManager($this->view->getViewHelperManager());
        $layout->setTemplatesPath($this->view->getTemplatesPath());
        $layout->setTemplate($layoutTemplate);
        $layout->set('content', $this->view->getContents());

        return $layout->getContents();
    }
}
