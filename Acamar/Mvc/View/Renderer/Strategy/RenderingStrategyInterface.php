<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Mvc\View\Renderer\Strategy;

use Acamar\Mvc\Event\MvcEvent;
use Acamar\Mvc\View\View;

/**
 * Interface RenderingStrategyInterface
 *
 * @package Acamar\Mvc\View\Renderer
 */
interface RenderingStrategyInterface
{
    /**
     * Sets the View object that will be used when rendering
     *
     * @param \Acamar\Mvc\View\View $view
     * @return RenderingStrategyInterface
     */
    public function setView(View $view);

    /**
     * Sets the event object
     *
     * @param MvcEvent $event
     * @return RenderingStrategyInterface
     */
    public function setEvent(MvcEvent $event);

    /**
     * Renders the View and returns the rendered content
     *
     * @return string
     */
    public function render();
}
