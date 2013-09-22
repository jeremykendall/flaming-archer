<?php

namespace FA\Tests\Authentication\Adapter;

use FA\Authentication\Adapter\DbAdapter;
use FA\Model\User;
use Zend\Authentication\Result;

class DbAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbAdapter
     */
    protected $adapter;

    /**
     * @var FA\Dao\UserDao
     */
    protected $dao;

    /**
     * @var array
     */
    protected $user;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = $this->getMockBuilder('FA\Dao\UserDao')
            ->disableOriginalConstructor()
            ->getMock();
        $this->adapter = new DbAdapter($this->dao);

        $data = array(
            'id' => '1',
            'email' => 'user@example.com',
            'password_hash' => '$2y$12$pZg9j8DBSIP2R/vfDzTQOeIt5n57r5VigCUl/HH.FrBOadi3YhdPS',
            'last_login' => null,
        );

        $this->user = new User($data);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->adapter = null;
        parent::tearDown();
    }

    public function testCreation()
    {
        $this->assertInstanceOf('FA\Authentication\Adapter\DbAdapter', $this->adapter);
    }

    /**
     * @covers FA\Authentication\Adapter\DbAdapter::authenticate
     */
    public function testAuthenticateSuccess()
    {
        $this->dao->expects($this->once())
            ->method('findByEmail')
            ->with($this->user->getEmail())
            ->will($this->returnValue($this->user));

        $this->adapter->setCredentials('user@example.com', 'password');
        $result = $this->adapter->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);
        $this->assertEquals($this->user, $result->getIdentity());
        $this->assertEquals(Result::SUCCESS, $result->getCode());
        $this->assertEquals(array(), $result->getMessages());
    }

    /**
     * @covers FA\Authentication\Adapter\DbAdapter::authenticate
     */
    public function testAuthenticateFailure()
    {
        $this->dao->expects($this->once())
            ->method('findByEmail')
            ->with($this->user->getEmail())
            ->will($this->returnValue($this->user));

        $this->adapter->setCredentials('user@example.com', 'badpassword');
        $result = $this->adapter->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);
        $this->assertEquals(array(), $result->getIdentity());
        $this->assertEquals(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
        $this->assertEquals(array('Invalid username or password provided'), $result->getMessages());
    }

    public function testAuthenticationUserNotFound()
    {
        $this->dao->expects($this->once())
            ->method('findByEmail')
            ->with('user@example.org')
            ->will($this->returnValue(false));

        $this->adapter->setCredentials('user@example.org', 'userdoesnotexist');
        $result = $this->adapter->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);
        $this->assertEquals(array(), $result->getIdentity());
        $this->assertEquals(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertEquals(array('Invalid username or password provided'), $result->getMessages());
    }
}
