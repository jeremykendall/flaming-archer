<?php

namespace FA\Tests\Middleware;

use FA\Middleware\GoogleAnalytics;

class GoogleAnalyticsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $auth;

    /**
     * @var string
     */
    protected $trackingId;

    protected function setUp()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '/'
        ));

        $this->auth = $this->getMock('Zend\Authentication\AuthenticationService');
        $this->trackingId = '1234';
        $this->domain = 'example.com';

        $this->app = new \Slim\Slim();
        $this->app->view(new \Slim\View());

        $this->app->get('/', function() {
            echo 'Success';
        });
    }

    public function testVisitorAnalyticsExist()
    {
        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $mw = new GoogleAnalytics($this->auth, $this->trackingId, $this->domain);
        $mw->setApplication($this->app);
        $mw->setNextMiddleware($this->app);
        $mw->call();

        $response = $this->app->response();
        $ga = $this->app->view()->getData('ga');

        $this->assertNotNull($ga);
        $this->assertInternalType('array', $ga);
        $this->assertEquals(2, count($ga));
        $this->assertEquals($this->trackingId, $ga['trackingId']);
        $this->assertEquals($this->domain, $ga['domain']);
    }

    public function testAdminAnalyticsNotExist()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '/'
        ));

        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $mw = new GoogleAnalytics($this->auth, $this->trackingId, $this->domain);
        $mw->setApplication($this->app);
        $mw->setNextMiddleware($this->app);
        $mw->call();

        $response = $this->app->response();
        $ga = $this->app->view()->getData('ga');

        $this->assertNull($ga);
    }

    public function testLoginPageAnalyticsNotExist()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '/login'
        ));

        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $mw = new GoogleAnalytics($this->auth, $this->trackingId, $this->domain);
        $mw->setApplication($this->app);
        $mw->setNextMiddleware($this->app);
        $mw->call();

        $response = $this->app->response();
        $ga = $this->app->view()->getData('ga');

        $this->assertNull($ga);
    }
}
