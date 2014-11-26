<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Directory;

use Acamar\Directory\Directory;
use PHPUnit_Framework_TestCase;

/**
 * Class DirectoryTest
 *
 * @package AcamarTest\Directory
 */
class DirectoryTest extends PHPUnit_Framework_TestCase
{
    protected static $layers = 3;
    protected static $deepFile = '';

    const TEST_DIR = 'test_cleanup_dir';
    const DS       = DIRECTORY_SEPARATOR;

    public static function setUpBeforeClass()
    {
        if (!is_dir(self::TEST_DIR)) {
            mkdir(self::TEST_DIR);
        }
    }

    public function setUp()
    {
        $last_dir = self::TEST_DIR;

        for ($i = 1; $i <= self::$layers; $i++) {
            $file = $last_dir . self::DS . md5(time()) . '.file';

            if (!is_file($file)) {
                touch($file);
                $file = realpath($file);
                $fh   = fopen($file, 'w');
                fwrite($fh, 'This is a test file for the directory cleanup');
                fclose($fh);
            }

            // Adding a second file for the whitelist test
            if ($i == 2) {
                $file = $last_dir . self::DS . 'whitelisted.file';

                if (!is_file($file)) {
                    touch($file);
                    $file = realpath($file);
                    $fh   = fopen($file, 'w');
                    fwrite($fh, 'Whitelist test');
                    fclose($fh);

                    self::$deepFile = $file;
                }
            }

            $last_dir = $last_dir . self::DS . md5(time());
            mkdir($last_dir);
            $last_dir = realpath($last_dir);
        }
    }

    public function testCleanUpWithDirTrailingSlash()
    {
        Directory::cleanup(self::TEST_DIR . self::DS);

        $this->assertTrue(count(scandir(self::TEST_DIR)) == 2);
    }

    public function testCleanUpWithDoubleDirTrailingSlash()
    {
        Directory::cleanup(self::TEST_DIR . self::DS . self::DS);

        $this->assertTrue(count(scandir(self::TEST_DIR)) == 2);
    }

    public function testCleanUpWithoutDirTrailingSlash()
    {
        Directory::cleanup(self::TEST_DIR);

        $this->assertTrue(count(scandir(self::TEST_DIR)) == 2);
    }

    /**
     * Whitelisted file is in root of cleaned folder
     */
    public function testCleanUpWithShallowWhitelist()
    {
        $file = self::TEST_DIR . self::DS . 'whitelisted.txt';

        // Creating the file
        touch($file);
        $file = realpath($file);
        $fh   = fopen($file, 'w');
        fwrite($fh, 'This is a test file for the directory cleanup');
        fclose($fh);

        Directory::cleanup(self::TEST_DIR, array('whitelisted.txt'));

        $this->assertTrue(count(scandir(self::TEST_DIR)) == 3);

        // Removing the file so that tearDownAfterClass works properly
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Whitelisted file is somewhere inside a folder (not the root folder)
     */
    public function testCleanUpWithDeepWhitelist()
    {
        Directory::cleanup(self::TEST_DIR, array('whitelisted.file'));

        $this->assertTrue(is_file(self::$deepFile));

        Directory::cleanup(self::TEST_DIR);
    }

    public static function tearDownAfterClass()
    {
        if (is_dir(self::TEST_DIR)) {
            rmdir(self::TEST_DIR);
        }
    }
}
