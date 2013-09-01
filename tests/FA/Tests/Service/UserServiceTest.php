<?php

namespace FA\Tests\Service;

use FA\Dao\UserDao;
use FA\Service\UserService;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserDao
     */
    private $dao;

    /**
     * @var AuthenticationService
     */
    private $auth;

    /**
     * @var EncryptedCookie Auth storage
     */
    private $storage;

    /**
     * @var AuthAdapter
     */
    private $authAdapter;

    /**
     * @var Result
     */
    private $result;

    /**
     * @var array User
     */
    private $user;

    /**
     * @var UserService
     */
    private $service;

    protected function setUp()
    {
        $this->dao = $this->getMockBuilder('FA\Dao\UserDao')
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = $this->getMockBuilder('Zend\Authentication\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->result = $this->getMockBuilder('Zend\Authentication\Result')
            ->disableOriginalConstructor()
            ->getMock();

        $this->storage = $this->getMockBuilder('FA\Authentication\Storage\EncryptedCookie')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authAdapter = $this->getMockBuilder('FA\Authentication\Adapter\DbAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = array(
            'id' => 1,
            'email' => 'user@example.com',
        );

        $this->service = new UserService($this->dao, $this->auth);
    }

    public function testUpdateEmail()
    {
        $updatedUser = array(
            'id' => 1,
            'email' => 'test@example.com',
        );

        $this->dao->expects($this->once())
            ->method('updateEmail')
            ->with($this->user['id'], $updatedUser['email'])
            ->will($this->returnValue($updatedUser));

        $this->auth->expects($this->once())
            ->method('clearIdentity');

        $this->auth->expects($this->once())
            ->method('getStorage')
            ->will($this->returnValue($this->storage));

        $this->storage->expects($this->once())
            ->method('write')
            ->with($updatedUser);

        $user = $this->service->updateEmail($this->user, $updatedUser['email']);

        $this->assertEquals($user['id'], $updatedUser['id']);
        $this->assertEquals($updatedUser['email'], $user['email']);
    }

    public function testAuthenticate()
    {
        $this->auth->expects($this->once())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->once())
            ->method('setCredentials')
            ->with($this->user['email'], 'password');

        $this->auth->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->service->authenticate($this->user['email'], 'password');
    }

    public function testClearIdentity()
    {
        $this->auth->expects($this->once())
            ->method('clearIdentity');

        $this->service->clearIdentity();
    }

    public function testChangePasswordThrowsExceptionIfNewIsFalsey()
    {
        $this->setExpectedException('InvalidArgumentException', 'New and confirm password are both required.');
        $this->service->changePassword($this->user['email'], 'current', '', 'not blank');
    }

    public function testChangePasswordThrowsExceptionIfConfirmIsFalsey()
    {
        $this->setExpectedException('InvalidArgumentException', 'New and confirm password are both required.');
        $this->service->changePassword($this->user['email'], 'current', 'not blank', '');
    }

    public function testChangePasswordThrowsExceptionIfNewAndConfirmDoNotMatch()
    {
        $this->setExpectedException('InvalidArgumentException', 'New and confirm passwords must match.');
        $this->service->changePassword($this->user['email'], 'current', 'not blank', 'is not blank');
    }

    public function testChangePasswordThrowsExceptionIfNewPassShorterThanEightChars()
    {
        $this->setExpectedException('InvalidArgumentException', 'Password must be at least 8 characters in length.');
        $this->service->changePassword($this->user['email'], 'current', 'short', 'short');
    }

    public function testChangePasswordThrowsExceptionIfCurrentPasswordIncorrect()
    {
        $this->setExpectedException('Exception', 'Your current password is incorrect.');

        $this->auth->expects($this->once())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->once())
            ->method('setCredentials')
            ->with($this->user['email'], 'current');

        $this->auth->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->service->changePassword($this->user['email'], 'current', 'not blank', 'not blank');
    }

    public function testChangePasswordThrowsExceptionIfIssueAuthenticatingWithNewPassword()
    {
        $this->setExpectedException('Exception', 'Your password was changed but there was an issue reauthenticating. PLease log out and back in with your new password.');

        $this->auth->expects($this->atLeastOnce())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->at(0))
            ->method('setCredentials')
            ->with($this->user['email'], 'current');

        $this->authAdapter->expects($this->at(1))
            ->method('setCredentials')
            ->with($this->user['email'], 'not blank');

        $this->auth->expects($this->atLeastOnce())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->at(0))
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->result->expects($this->at(1))
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->service->changePassword($this->user['email'], 'current', 'not blank', 'not blank');
    }

    public function testChangePasswordSucceeds()
    {
        $this->auth->expects($this->atLeastOnce())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->at(0))
            ->method('setCredentials')
            ->with($this->user['email'], 'password');

        $this->authAdapter->expects($this->at(1))
            ->method('setCredentials')
            ->with($this->user['email'], 'not blank');

        $this->auth->expects($this->atLeastOnce())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->atLeastOnce())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->dao->expects($this->once())
            ->method('findByEmail')
            ->with($this->user['email'])
            ->will($this->returnValue($this->user));

        $this->dao->expects($this->once())
            ->method('changePassword')
            ->with(
                $this->user['id'], 
                $this->matchesRegularExpression('/^\$2y\$10\$[a-zA-Z0-9\.\/]*$/')
            )
            ->will($this->returnValue($this->user));

        $user = $this->service->changePassword($this->user['email'], 'password', 'not blank', 'not blank');
        $this->assertEquals($this->user, $user);
    }
}
