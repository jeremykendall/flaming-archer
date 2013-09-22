<?php

namespace FA\Tests\DI;

use FA\DI\Container;
use FA\Model\Photo\Photo;

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

    public function testMetaTagsCreation()
    {
        $this->container['request'] = $this->getMockBuilder('Slim\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container['image'] = new Photo();

        $this->assertInstanceOf('FA\Social\MetaTags', $this->container['metaTags']);
    }
}
