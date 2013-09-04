<?php

namespace FA\Tests\Middleware;

use FA\Middleware\Navigation;

class NavigationTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitorNavigation()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '/'
        ));

        $app = new \Slim\Slim();
        $app->view(new \Slim\View());

        $app->get('/', function() {
            echo 'Success';
        });

        $auth = $this->getMock('Zend\Authentication\AuthenticationService');

        $auth->expects($this->once())
                ->method('hasIdentity')
                ->will($this->returnValue(false));

        $mw = new Navigation($auth);
        $mw->setApplication($app);
        $mw->setNextMiddleware($app);
        $mw->call();

        $response = $app->response();
        $navigation = $app->view()->getData('navigation');

        $this->assertNotNull($navigation);
        $this->assertInternalType('array', $navigation);
        $this->assertEquals(2, count($navigation));

        $this->assertEquals('Home', $navigation[0]['caption']);
        $this->assertEquals('/', $navigation[0]['href']);
        $this->assertEquals('active', $navigation[0]['class']);

        $this->assertEquals('Login', $navigation[1]['caption']);
        $this->assertEquals('/login', $navigation[1]['href']);
        $this->assertEquals('', $navigation[1]['class']);
    }

    public function testAdminNavigation()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '',
            'PATH_INFO' => '/admin'
        ));

        $app = new \Slim\Slim();
        $app->view(new \Slim\View());

        $app->get('/admin', function() {
            echo 'Success';
        });

        $auth = $this->getMock('Zend\Authentication\AuthenticationService');

        $auth->expects($this->once())
                ->method('hasIdentity')
                ->will($this->returnValue(true));

        $mw = new Navigation($auth);
        $mw->setApplication($app);
        $mw->setNextMiddleware($app);
        $mw->call();

        $response = $app->response();
        $navigation = $app->view()->getData('navigation');

        $this->assertNotNull($navigation);
        $this->assertInternalType('array', $navigation);
        $this->assertEquals(4, count($navigation));

        $this->assertEquals('Home', $navigation[0]['caption']);
        $this->assertEquals('/', $navigation[0]['href']);
        $this->assertEquals('', $navigation[0]['class']);

        $this->assertEquals('Admin', $navigation[1]['caption']);
        $this->assertEquals('/admin', $navigation[1]['href']);
        $this->assertEquals('active', $navigation[1]['class']);

        $this->assertEquals('Settings', $navigation[2]['caption']);
        $this->assertEquals('/admin/settings', $navigation[2]['href']);
        $this->assertEquals('', $navigation[2]['class']);

        $this->assertEquals('Logout', $navigation[3]['caption']);
        $this->assertEquals('/logout', $navigation[3]['href']);
        $this->assertEquals('', $navigation[3]['class']);
    }
}
