<?php

namespace FA\Tests\DI;

use FA\DI\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $config;
    protected $container;

    protected function setUp()
    {
        $this->config = require APPLICATION_PATH . '/config.dist.php';
        $this->container = new Container($this->config);
    }

    public function testContainerCreation()
    {
        $this->assertInstanceOf('Pimple', $this->container);
    }

    public function testGoogleAnalyticsReturnsNullIfConfigKeysEmtpy()
    {
        $config = require APPLICATION_PATH . '/config.dist.php';
        $config['googleAnalyticsTrackingId'] = '';
        $config['googleAnalyticsDomain'] = '';

        $container = new Container($config);
        
        $this->assertNull($container['googleAnalyticsMiddleware']);
    }

    public function testGoogleAnalyticsReturnsMiddlewareIfConfigKeysProvided()
    {
        $config = require APPLICATION_PATH . '/config.dist.php';
        $config['googleAnalyticsTrackingId'] = '1234';
        $config['googleAnalyticsDomain'] = 'example.com';

        $container = new Container($config);
        
        $this->assertInstanceOf('FA\Middleware\GoogleAnalytics', $container['googleAnalyticsMiddleware']);
    }
}
