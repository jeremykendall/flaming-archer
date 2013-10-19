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

        $this->assertEquals('Feed', $navigation[1]['caption']);
        $this->assertEquals('/feed', $navigation[1]['href']);
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
        $this->assertEquals(5, count($navigation));

        $expected = array (
            0 => 
            array (
                'caption' => 'Home',
                'href' => '/',
                'class' => '',
            ),
            1 => 
            array (
                'caption' => 'Feed',
                'href' => '/feed',
                'class' => '',
            ),
            2 => 
            array (
                'caption' => 'Admin',
                'href' => '/admin',
                'class' => 'active',
            ),
            3 => 
            array (
                'caption' => 'Settings',
                'href' => '/admin/settings',
                'class' => '',
            ),
            4 => 
            array (
                'caption' => 'Logout',
                'href' => '/logout',
                'class' => '',
            ),
        );

        $this->assertEquals($expected, $navigation);
    }
}
