<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2015
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Log;

/**
 * Class LogLevel
 *
 * @package Acamar\Log
 */
abstract class LogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    /**
     * @param string|null $maxLevel
     * @return array
     */
    public static function getLevels($maxLevel = null)
    {
        if (null === $maxLevel) {
            return static::getLevelPriority();
        }

        $allLevels = static::getLevelPriority();

        return array_slice($allLevels, array_search($maxLevel, $allLevels));
    }

    /**
     * Returns an array with the levels ordered by priority
     *
     * @return array
     */
    protected static function getLevelPriority()
    {
        return array(
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG
        );
    }
} 
