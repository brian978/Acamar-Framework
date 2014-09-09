<?php
/**
 * Acamar-PHP
 *
 * @link      https://github.com/brian978/Acamar-PHP
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTest\Url;

use Acamar\Url\Url;
use PHPUnit_Framework_TestCase;

/**
 * Class UrlTest
 *
 * @package AcamarTest\Url
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $_SERVER['HTTPS']       = 'On';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '';
    }

    /**
     * @expectedException \Acamar\Url\Exception\RuntimeException
     * @expectedExceptionMessage Invalid site root URL.
     */
    public function testSiteRootIsNotSet()
    {
        new Url();
    }

    /**
     * @expectedException \Acamar\Url\Exception\RuntimeException
     * @expectedExceptionMessage Invalid SSL site root URL
     */
    public function testSiteRootSslIsInvalid()
    {
        $config = array(
            'site_root' => 'http://localhost',
            'require_ssl' => true
        );

        new Url($config);
    }

    public function testUrlGeneration()
    {
        $config = array(
            'site_root' => 'http://localhost',
        );

        $url = new Url($config);

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test', $link);
    }

    public function testUrlGenerationUsingInvoke()
    {
        $config = array(
            'site_root' => 'http://localhost',
        );

        $url = new Url($config);

        $link = $url('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test', $link);
    }

    public function testUrlGenerationUsingPreserveGet()
    {
        // Some get params
        $_GET['param2'] = 'test2';
        $_GET['param3'] = 'test3';

        $config = array(
            'site_root' => 'http://localhost',
        );

        $url = new Url($config);

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test'), true);

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test&param2=test2&param3=test3', $link);
    }

    public function testUrlGenerationUsingPreserveGetWithOverride()
    {
        // Some get params
        $_GET['param2'] = 'test2';
        $_GET['param3'] = 'test3';

        $config = array(
            'site_root' => 'http://localhost',
        );

        $url = new Url($config);

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test', 'param2' => 'test2new'), true);

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test&param2=test2new&param3=test3', $link);
    }

    public function testUrlGenerationUsingPersistentParams()
    {
        // Some get params
        $_GET['param2'] = 'test2';
        $_GET['param3'] = 'test3';

        $config = array(
            'site_root' => 'http://localhost',
            'persistent_params' => array(
                'param2'
            )
        );

        $url = new Url($config);

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test&param2=test2', $link);
    }

    public function testUrlGenerationUsingPersistentParamsWithOverride()
    {
        // Some get params
        $_GET['param2'] = 'test2';
        $_GET['param3'] = 'test3';

        $config = array(
            'site_root' => 'http://localhost',
            'persistent_params' => array(
                'param2'
            )
        );

        $url = new Url($config);

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test', 'param2' => 'test2new'));

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test&param2=test2new', $link);
    }

    public function testUrlGenerationUsingDisableSsl()
    {
        $config = array(
            'site_root' => 'http://localhost',
            'site_root_ssl' => 'https://localhost',
            'require_ssl' => true
        );

        $url = new Url($config);

        $url->disableSSL();

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test', $link);
    }

    public function testSslUrlGeneration()
    {
        $config = array(
            'site_root' => 'http://localhost',
            'site_root_ssl' => 'https://localhost',
        );

        $url = new Url($config);

        $link = $url->ssl()->get('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('https://localhost/?controller=contact&action=index&param1=test', $link);
    }

    public function testSslUrlGenerationUsingConfig()
    {
        $config = array(
            'site_root' => 'http://localhost',
            'site_root_ssl' => 'https://localhost',
            'require_ssl' => true
        );

        $url = new Url($config);

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('https://localhost/?controller=contact&action=index&param1=test', $link);
    }

    public function testSslUrlGenerationUsingEnableSsl()
    {
        $config = array(
            'site_root' => 'http://localhost',
            'site_root_ssl' => 'https://localhost',
        );

        $url = new Url($config);

        $url->enableSSL();

        $link = $url->get('contact', array('action' => 'index', 'param1' => 'test'));

        $this->assertEquals('https://localhost/?controller=contact&action=index&param1=test', $link);
    }

    public function testGenerationWithNoParams()
    {
        $config = array(
            'site_root' => 'http://localhost'
        );

        $url = new Url($config);

        $link = $url->get('contact');

        $this->assertEquals('http://localhost/?controller=contact', $link);
    }

    public function testGenerationWithParamsButNoAction()
    {
        $config = array(
            'site_root' => 'http://localhost'
        );

        $url = new Url($config);

        $link = $url->get('contact', array('param1' => 'test'));

        $this->assertEquals('http://localhost/?controller=contact&action=index&param1=test', $link);
    }
}
