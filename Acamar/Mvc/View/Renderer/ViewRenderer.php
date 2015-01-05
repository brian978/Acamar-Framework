<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc\View\Renderer;

use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\Renderer\Strategy\RenderingStrategyInterface;
use Acamar\Mvc\View\View;

/**
 * Class ViewRenderer
 *
 * @package Acamar\Mvc\View\Renderer
 */
class ViewRenderer
{
    /**
     * @var RenderingStrategyInterface
     */
    protected $renderingStrategy = null;

    /**
     * @var MvcEvent
     */
    protected $event = null;

    /**
     * @var \Acamar\Config\Config
     */
    protected $config = null;

    /**
     * @param MvcEvent $event
     */
    public function __construct(MvcEvent $event)
    {
        $this->event  = $event;
        $this->config = $event->getTarget()->getConfig();
    }

    /**
     * @param \Acamar\Mvc\View\Renderer\Strategy\RenderingStrategyInterface $renderingStrategy
     * @return $this
     */
    public function setRenderingStrategy(RenderingStrategyInterface $renderingStrategy)
    {
        $this->renderingStrategy = $renderingStrategy;

        return $this;
    }

    /**
     * Renders the data using a rendering strategy
     *
     * @return void
     */
    public function render()
    {
        $view = $this->event->getView();
        if ($view instanceof View) {
            $this->renderingStrategy->setView($view);

            // Configure the ViewHelperManager
            $viewHelperManager = $view->getViewHelperManager();
            $viewHelperManager->setConfig($this->config);
            $viewHelperManager->setEvent($this->event);

            // Configure the View with the template
            $route = $this->event->getRoute();
            if (isset($this->config['view']['paths'][$route->getModuleName()])) {
                $view->setTemplatesPath($this->config['view']['paths'][$route->getModuleName()]);
            }

            $view->setTemplate($route->getControllerName() . DIRECTORY_SEPARATOR . $route->getActionName());

            // Setting the rendered view as the response
            $this->event->getResponse()->setBody($this->renderingStrategy->render());
        }
    }
}
