<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Archive;

use Acamar\Archive\Zip;
use Acamar\Directory\Directory;
use PHPUnit_Framework_TestCase;

/**
 * Class ZipTest
 *
 * @package AcamarTest\Archive
 */
class ZipTest extends PHPUnit_Framework_TestCase
{
    protected static $layers = 3;

    /**
     * @var \Acamar\Archive\Zip
     */
    protected $zip;

    const TEST_DIR     = 'test_archive_dir';
    const TEST_FILE    = 'random_file.txt';
    const ARCHIVE_FILE = 'testArch.zip';
    const DS           = DIRECTORY_SEPARATOR;

    public static function setUpBeforeClass()
    {
        if (!is_dir(self::TEST_DIR)) {
            mkdir(self::TEST_DIR);

            $last_dir = self::TEST_DIR;

            for ($i = 1; $i <= self::$layers; $i++) {
                $file = $last_dir . self::DS . self::TEST_FILE;

                if (!is_file($file)) {
                    touch($file);
                    $file = realpath($file);
                    $fh   = fopen($file, 'w');
                    fwrite($fh, 'This is a test file for the archive creation');
                    fclose($fh);
                }

                $last_dir = $last_dir . self::DS . md5(time());
                mkdir($last_dir);
                $last_dir = realpath($last_dir);
            }
        }
    }

    public function setUp()
    {
        $this->zip = new Zip();
    }

    public function testTestDirCreated()
    {
        $this->assertTrue(is_dir(self::TEST_DIR));
    }

    /**
     * @depends testTestDirCreated
     */
    public function testTestFileCreated()
    {
        $this->assertTrue(is_file(realpath(self::TEST_DIR . self::DS . self::TEST_FILE)));
    }

    /**
     * @depends testTestFileCreated
     */
    public function testCreateArchive()
    {
        $this->zip->pack(self::TEST_DIR, self::ARCHIVE_FILE);

        $this->assertTrue(is_file(self::ARCHIVE_FILE));
    }

    /**
     * @depends testCreateArchive
     */
    public function testUnpackArchive()
    {
        $this->zip->unpack(self::ARCHIVE_FILE, self::TEST_DIR . '_unpacked');

        $original = realpath(basename(self::TEST_DIR));
        $copy     = realpath(self::TEST_DIR . '_unpacked' . self::DS . basename(self::TEST_DIR));

        $this->assertTrue($this->compareDirectories($original, $copy));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testArchiveNotFound()
    {
        $this->zip->unpack('dummy_archive.zip', self::TEST_DIR . '_unpacked');
    }

    /**
     * @param $original
     * @param $copy
     * @return bool
     */
    protected function compareDirectories($original, $copy)
    {
        $result = true;

        foreach (scandir($original) as $path) {
            if ($path != '.' && $path != '..') {
                $fullPath = $original . self::DS . $path;

                if (is_file($fullPath)) {
                    if (is_file($copy . self::DS . $path)) {
                        $originalSize = filesize($fullPath);
                        $copySize     = filesize($copy . self::DS . $path);

                        if ($originalSize == false
                            || $copySize == false
                            || $originalSize !== $copySize
                        ) {
                            $result = false;
                        }
                    } else {
                        $result = false;
                    }
                } else if (is_dir($fullPath)) {
                    $copyPath = $copy . self::DS . $path;

                    if (is_dir($copyPath)) {
                        $result = $this->compareDirectories($fullPath, $copyPath);
                    } else {
                        $result = false;
                    }
                }
            }

            if ($result == false) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param string $dir
     */
    public static function cleanUpDir($dir)
    {
        Directory::cleanup($dir);
    }

    public static function tearDownAfterClass()
    {
        $cleanup = array(
            self::TEST_DIR,
            self::TEST_DIR . '_unpacked',
        );

        foreach ($cleanup as $dir) {
            self::cleanUpDir($dir);
        }

        if (is_file(self::ARCHIVE_FILE)) {
            unlink(self::ARCHIVE_FILE);
        }
    }
}
