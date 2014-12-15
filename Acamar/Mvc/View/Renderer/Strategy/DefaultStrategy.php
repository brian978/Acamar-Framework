<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc\View\Renderer\Strategy;

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
        $response = $this->event->getResponse();
        if (false === $response->getContentType()) {
            $response->setContentType('text/html');
        }

        // We will need a layout
        $layout = new View();
        $layout->setTemplatesPath($this->view->getTemplatesPath());
        $layout->setTemplate('layout\layout.phtml');
        $layout->set('content', $this->view->getContents());

        return $layout->getContents();
    }
}
