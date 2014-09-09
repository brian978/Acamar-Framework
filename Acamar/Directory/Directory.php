<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Directory;

/**
 * Class Directory
 *
 * @package Acamar\Directory
 */
class Directory
{
    /**
     * Cleans up a given directory except for the files in the whitelist
     *
     * @param string $dir
     * @param array $whitelist
     * @return void
     */
    public static function cleanup($dir, array $whitelist = array())
    {
        // ==== Reading the files from the directory and deleting the ones not present in the whitelist ==== //
        if (is_dir($dir)) {
            $dir = realpath($dir) . DIRECTORY_SEPARATOR;

            // ==== Opening the directory ==== //
            $dh = opendir($dir);

            // ==== Checking if the directory was opened successfully ==== //
            if ($dh != false) {
                while (($file = readdir($dh)) !== false) {
                    // ==== Checking if the file exists in the whitelist and it's different from dot ==== //
                    if ($file != '.' && $file != '..' && !in_array($file, $whitelist)) {
                        // Recursively removing the directories
                        if (is_dir($dir . $file)) {
                            self::cleanup($dir . $file, $whitelist);

                            // Directory must be empty for deletion to occur
                            if (count(scandir($dir . $file)) == 2) {
                                rmdir($dir . $file);
                            }
                        } else if (is_file($dir . $file)) {
                            unlink($dir . $file);
                        }
                    }
                }
            }
        }
    }
}
