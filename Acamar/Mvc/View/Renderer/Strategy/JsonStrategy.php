<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc\View\Renderer\Strategy;

/**
 * Class JsonStrategy
 *
 * @package Acamar\Mvc\View\Renderer
 */
class JsonStrategy extends AbstractRenderingStrategy implements RenderingStrategyInterface
{
    /**
     * Renders the View and returns the rendered content
     *
     * @return string
     */
    public function render()
    {
        return json_encode($this->view->toArray());
    }
}
