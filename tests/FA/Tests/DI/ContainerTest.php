<?php

namespace FA\Tests\DI;

use FA\DI\Container;
use FA\Model\Photo\Photo;
use FA\Tests\CustomTestCase;

class ContainerTest extends CustomTestCase
{
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = new Container($this->config);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testContainerCreation()
    {
        $this->assertInstanceOf('Pimple', $this->container);
    }

    public function testGoogleAnalyticsReturnsNullIfConfigKeysEmtpy()
    {
        $this->config['googleAnalyticsTrackingId'] = '';
        $this->config['googleAnalyticsDomain'] = '';

        $container = new Container($this->config);
        
        $this->assertNull($container['googleAnalyticsMiddleware']);
    }

    public function testGoogleAnalyticsReturnsMiddlewareIfConfigKeysProvided()
    {
        $this->config['googleAnalyticsTrackingId'] = '1234';
        $this->config['googleAnalyticsDomain'] = 'example.com';

        $container = new Container($this->config);
        
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
