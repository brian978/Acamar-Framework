<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
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
     * @param MvcEvent $event
     */
    public function __construct(MvcEvent $event)
    {
        $this->event = $event;
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

            // Setting the rendered view as the response
            $this->event->getResponse()->setBody($this->renderingStrategy->render());
        }
    }
}
