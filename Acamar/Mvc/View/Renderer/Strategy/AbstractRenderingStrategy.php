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
 * Class AbstractRenderingStrategy
 *
 * @package Acamar\Mvc\View\Renderer
 */
abstract class AbstractRenderingStrategy implements RenderingStrategyInterface
{
    /**
     * @var \Acamar\Mvc\View\View
     */
    protected $view = null;

    /**
     * @var MvcEvent
     */
    protected $event = null;

    /**
     * @param MvcEvent $event
     */
    public function __construct(MvcEvent $event)
    {
        $this->setEvent($event);
    }

    /**
     * Sets the View object that will be used when rendering
     *
     * @param \Acamar\Mvc\View\View $view
     * @return RenderingStrategyInterface
     */
    public function setView(View $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Sets the event object
     *
     * @param MvcEvent $event
     * @return RenderingStrategyInterface
     */
    public function setEvent(MvcEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
