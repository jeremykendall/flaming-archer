<?php

namespace FA\Tests\Middleware;

use FA\DI\Container;
use FA\Middleware\Settings;
use FA\Tests\ConfigTestCase;

class SettingsTest extends ConfigTestCase
{
    /**
     * @var Slim
     */
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        \Slim\Environment::mock(array(
            'SERVER_NAME' => 'example.com',
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '/'
        ));

        $this->app = new \Slim\Slim();
        $this->app->view(new \Slim\View());

        $this->app->get('/feed', function () {
            echo 'Success';
        })->name('feed');

        $this->container = new Container($this->config);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testCallSetsBaseUrlAndFeedUri()
    {
        $this->assertNull($this->container['baseUrl']);
        $this->assertNull($this->container['feedUri']);

        $mw = new Settings($this->container);
        $mw->setApplication($this->app);
        $mw->setApplication($this->app);
        $mw->setNextMiddleware($this->app);
        $mw->call();

        $this->assertSame($this->app->request->getUrl(), $this->container['baseUrl']);
        $this->assertSame($this->app->urlFor('feed'), $this->container['feedUri']);
    }
}
