<?php

namespace Fa\Tests\Hook;

use Fa\Hook\SetupHook;

class SetupHookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fa\Dao\UserDao
     */
    private $dao;

    /**
     * @var Slim\Slim
     */
    private $app;

    /**
     * @var SetupHook
     */
    private $hook;

    protected function setUp()
    {
        parent::setUp();
        $this->app = $this->getMock('Slim\Slim', array(), array(), '', false);
        $this->dao = $this->getMock('Fa\Dao\UserDao', array(), array(), '', false);
        $this->hook = new SetupHook($this->app, $this->dao);
    }

    protected function tearDown()
    {
        $this->hook = null;
        parent::tearDown();
    }

    public function testConfirmSetupDoesNotRedirectIfAppIsConfigured()
    {
        $this->dao->expects($this->once())
            ->method('userExists')
            ->will($this->returnValue(true));

        $this->app->expects($this->never())
            ->method('redirect');

        $this->hook->confirmSetup();
    }

    public function testConfirmSetupRedirectsUserToSetupIfAppNotConfigured()
    {
        $this->dao->expects($this->once())
            ->method('userExists')
            ->will($this->returnValue(false));

        $this->app->expects($this->once())
            ->method('redirect')
            ->with('/setup');

        $this->hook->confirmSetup();
    }
}
