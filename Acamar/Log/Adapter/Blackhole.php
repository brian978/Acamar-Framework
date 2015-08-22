<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2015
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Log\Adapter;

use Acamar\Log\LogEntry;

/**
 * Class Blackhole
 *
 * @package Acamar\Log\Adapter
 */
class Blackhole implements LogAdapterInterface
{
    /**
     * @param LogEntry $log
     * @return null
     */
    public function add(LogEntry $log)
    {
        // Since this is a black hole adapter we don't need to do anything here
    }
}
