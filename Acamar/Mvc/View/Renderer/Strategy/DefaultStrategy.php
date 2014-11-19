<?php
/**
 * SlimMVC
 *
 * @link https://github.com/brian978/SlimMVC
 * @copyright Copyright (c) 2014
 * @license Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Mvc\View\Renderer\Strategy;

use Acamar\Http\Headers;
use Acamar\Mvc\View\View;

/**
 * Class DefaultStrategy
 *
 * @package Acamar\Mvc\View\Renderer
 */
class DefaultStrategy extends AbstractRenderingStrategy implements RenderingStrategyInterface
{
    /**
     * Renders the View and returns the rendered content
     *
     * @return string
     */
    public function render()
    {
        // Updating the response headers to match the type of content
        $this->event->getResponse()->getHeaders()->set(Headers::CONTENT_TYPE, 'text/html');

        // We will need a layout
        $layout = new View();
        $layout->setTemplatesPath($this->view->getTemplatesPath());
        $layout->setTemplate('layout\layout.phtml');
        $layout->set('content', $this->view->getContents());

        return $layout->getContents();
    }
}
