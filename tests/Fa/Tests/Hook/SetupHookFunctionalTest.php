<?php

namespace Fa\Tests\Hook;

use Fa\Hook\SetupHook;

class SetupHookFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fa\Dao\UserDao
     */
    private $dao;

    protected function setUp()
    {
        parent::setUp();
        $this->dao = $this->getMock('Fa\Dao\UserDao', array(), array(), '', false);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testVisitHomePageSucceedsIfAppIsConfigured()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/'
        ));

        $app = new \Slim\Slim();

        $setupHook = new SetupHook($app, $this->dao);

        $app->hook('slim.before.router', function () use ($setupHook) {
            $setupHook->confirmSetup();
        });

        $app->get('/', function() { echo 'Welcome'; });

        $this->dao->expects($this->once())
            ->method('userExists')
            ->will($this->returnValue(true));

        $app->call();

        $response = $app->response();
        $this->assertTrue($response->isOk());
    }

    public function testVisitHomePageRedirectsToSetupIfAppNotConfigured()
    {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/'
        ));

        $app = new \Slim\Slim();

        $setupHook = new SetupHook($app, $this->dao);

        $app->hook('slim.before.router', function () use ($setupHook) {
            $setupHook->confirmSetup();
        });

        $this->dao->expects($this->once())
            ->method('userExists')
            ->will($this->returnValue(false));

        $app->call();

        $response = $app->response();
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(302, $response->status());
        $this->assertEquals('/setup', $response->header('location'));
    }
}
