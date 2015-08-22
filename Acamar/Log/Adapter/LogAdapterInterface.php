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
 * Interface LogAdapterInterface
 *
 * @package Acamar\Log\Adapter
 */
interface LogAdapterInterface
{
    /**
     * @param LogEntry $log
     * @return null
     */
    public function add(LogEntry $log);
} 
