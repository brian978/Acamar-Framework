<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Mvc\View\Renderer\Strategy;

use Acamar\Mvc\Event\MvcEvent;

/**
 * Class RenderingStrategyFactory
 *
 * @package Acamar\Mvc\View\Renderer\Factory
 */
class RenderingStrategyFactory
{
    /**
     * @param MvcEvent $event
     * @return RenderingStrategyInterface
     */
    public static function factory(MvcEvent $event)
    {
        $contentType = $event->getResponse()->getContentType();
        switch ($contentType) {
            case 'text/json':
                return new JsonStrategy($event);

            default:
                return new DefaultStrategy($event);
        }
    }
}
