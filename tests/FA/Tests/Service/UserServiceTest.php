<?php

namespace FA\Tests\Service;

use FA\Dao\UserDao;
use FA\Model\User;
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
     * @var Zend\Authentication\Storage\StorageInterface Auth storage
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

        $this->storage = $this->getMockBuilder('Zend\Authentication\Storage\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authAdapter = $this->getMockBuilder('FA\Authentication\Adapter\DbAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = new User(array(
            'id' => 1,
            'email' => 'user@example.com',
        ));

        $this->service = new UserService($this->dao, $this->auth);
    }

    protected function tearDown()
    {
        $this->service = null;
    }

    public function testUpdateEmail()
    {
        $updatedUser = new User(array(
            'id' => 1,
            'email' => 'test@example.com',
        ));

        $this->user->setEmail($updatedUser->getEmail());

        $this->dao->expects($this->once())
            ->method('updateEmail')
            ->with($this->user)
            ->will($this->returnValue($updatedUser));

        $this->auth->expects($this->once())
            ->method('clearIdentity');

        $this->auth->expects($this->once())
            ->method('getStorage')
            ->will($this->returnValue($this->storage));

        $this->storage->expects($this->once())
            ->method('write')
            ->with($updatedUser);

        $this->user->setEmail($updatedUser->getEmail());
        $user = $this->service->updateEmail($this->user);

        $this->assertEquals($user->getId(), $updatedUser->getId());
        $this->assertEquals($updatedUser->getEmail(), $user->getEmail());
    }

    public function testCheckPassword()
    {
        $this->auth->expects($this->once())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->once())
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'password');

        $this->authAdapter->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->service->checkPassword($this->user->getEmail(), 'password');
    }

    public function testAuthenticate()
    {
        $this->auth->expects($this->once())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->once())
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'password');

        $this->auth->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->service->authenticate($this->user->getEmail(), 'password');
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
        $this->service->changePassword($this->user->getEmail(), 'current', '', 'not blank');
    }

    public function testChangePasswordThrowsExceptionIfConfirmIsFalsey()
    {
        $this->setExpectedException('InvalidArgumentException', 'New and confirm password are both required.');
        $this->service->changePassword($this->user->getEmail(), 'current', 'not blank', '');
    }

    public function testChangePasswordThrowsExceptionIfNewAndConfirmDoNotMatch()
    {
        $this->setExpectedException('InvalidArgumentException', 'New and confirm passwords must match.');
        $this->service->changePassword($this->user->getEmail(), 'current', 'not blank', 'is not blank');
    }

    public function testChangePasswordThrowsExceptionIfNewPassShorterThanEightChars()
    {
        $this->setExpectedException('InvalidArgumentException', 'Password must be at least 8 characters in length.');
        $this->service->changePassword($this->user->getEmail(), 'current', 'short', 'short');
    }

    public function testChangePasswordThrowsExceptionIfCurrentPasswordIncorrect()
    {
        $this->setExpectedException('Exception', 'Your current password is incorrect.');

        $this->auth->expects($this->once())
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->once())
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'current');

        $this->authAdapter->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->service->changePassword($this->user->getEmail(), 'current', 'not blank', 'not blank');
    }

    public function testChangePasswordThrowsExceptionIfIssueAuthenticatingWithNewPassword()
    {
        $this->setExpectedException('Exception', 'Your password was changed but there was an issue reauthenticating. PLease log out and back in with your new password.');

        $this->auth->expects($this->at(0))
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->at(0))
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'current');

        $this->authAdapter->expects($this->at(1))
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->at(0))
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->dao->expects($this->once())
            ->method('findByEmail')
            ->with($this->user->getEmail())
            ->will($this->returnValue($this->user));

        $this->dao->expects($this->once())
            ->method('changePassword')
            ->with(
                $this->user->getId(),
                $this->matchesRegularExpression('/^\$2y\$10\$[a-zA-Z0-9\.\/]*$/')
            )
            ->will($this->returnValue($this->user));

        $this->auth->expects($this->at(1))
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->at(2))
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'not blank');

        $this->auth->expects($this->at(2))
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->at(1))
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->service->changePassword($this->user->getEmail(), 'current', 'not blank', 'not blank');
    }

    public function testChangePasswordSucceeds()
    {
        $this->auth->expects($this->at(0))
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->at(0))
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'password');

        $this->authAdapter->expects($this->at(1))
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->at(0))
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->dao->expects($this->once())
            ->method('findByEmail')
            ->with($this->user->getEmail())
            ->will($this->returnValue($this->user));

        $this->dao->expects($this->once())
            ->method('changePassword')
            ->with(
                $this->user->getId(),
                $this->matchesRegularExpression('/^\$2y\$10\$[a-zA-Z0-9\.\/]*$/')
            )
            ->will($this->returnValue($this->user));

        $this->auth->expects($this->at(1))
            ->method('getAdapter')
            ->will($this->returnValue($this->authAdapter));

        $this->authAdapter->expects($this->at(2))
            ->method('setCredentials')
            ->with($this->user->getEmail(), 'not blank');

        $this->auth->expects($this->at(2))
            ->method('authenticate')
            ->will($this->returnValue($this->result));

        $this->result->expects($this->at(1))
            ->method('isValid')
            ->will($this->returnValue(true));

        $user = $this->service->changePassword($this->user->getEmail(), 'password', 'not blank', 'not blank');
        $this->assertEquals($this->user, $user);
    }

    public function testGetLoggedInUserReturnsLoggedInUser()
    {
        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($this->user));

        $user = $this->service->getLoggedInUser();

        $this->assertEquals($this->user, $user);
    }

    public function testGetLoggedInUserReturnsNullIfNoUserLoggedIn()
    {
        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $user = $this->service->getLoggedInUser();

        $this->assertNull($user);
    }

    public function testCreateUserSucceeds()
    {
        $passwordHash = 'Corned beef password hash';

        $this->dao->expects($this->once())
            ->method('createUser')
            ->with(
                $this->user->getEmail(),
                $this->matchesRegularExpression('/^\$2y\$10\$[a-zA-Z0-9\.\/]*$/')
            )
            ->will($this->returnValue($this->user));

        $user = $this->service->createUser($this->user->getEmail(), 'password', 'password');
        $this->assertEquals($this->user, $user);
    }

    public function testCreateUserPasswordsNotProvided()
    {
        $this->setExpectedException('InvalidArgumentException', 'Password and confirm password are both required.');
        $user = $this->service->createUser($this->user->getEmail(), null, null);
    }

    public function testCreateUserPasswordsNotMatch()
    {
        $this->setExpectedException('InvalidArgumentException', 'Passwords must match.');
        $user = $this->service->createUser($this->user->getEmail(), 'blahblahblah', 'blahblahbla');
    }

    public function testCreateUserPasswordsTooShort()
    {
        $this->setExpectedException('InvalidArgumentException', 'Password must be at least 8 characters in length.');
        $user = $this->service->createUser($this->user->getEmail(), 'blah', 'blah');
    }
}
