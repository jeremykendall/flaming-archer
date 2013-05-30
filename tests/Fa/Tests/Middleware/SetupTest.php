<?php

namespace Fa\Tests\Middleware;

use Fa\Middleware\Setup;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Setup
     */
    private $middleware;

    /**
     * @var Fa\Dao\UserDao
     */
    private $dao;

    protected function setUp()
    {
        parent::setUp();
        $this->dao = $this->getMock('Fa\Dao\UserDao', array(), array(), '', false);
        $this->middleware = new Setup($this->dao);
    }

    protected function tearDown()
    {
        $this->middleware = null;
        parent::tearDown();
    }

    public function testVisitHomePageSucceedsIfAppIsConfigured()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/'
        ));

        $app = new \Slim\Slim();

        $app->get('/', function() {
            echo 'Welcome';
        });

        $this->dao->expects($this->once())
            ->method('userExists')
            ->will($this->returnValue(true));

        $this->middleware->setApplication($app);
        $this->middleware->setNextMiddleware($app);
        $this->middleware->call();
        $response = $app->response();
        $this->assertTrue($response->isOk());
    }

    public function testVisitHomepageRedirectsToSetupIfAppNotConfigured()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/'
        ));

        $app = new \Slim\Slim();

        $app->get('/', function() {
            echo 'Head on over to setup, will ya?';
        });

        $this->dao->expects($this->once())
            ->method('userExists')
            ->will($this->returnValue(false));

        $this->middleware->setApplication($app);
        $this->middleware->setNextMiddleware($app);
        $this->middleware->call();
        $response = $app->response();
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(302, $response->status());
        $this->assertEquals('/setup', $response->header('location'));
    }
}
