<?php

namespace FA\Tests\Dao;

use FA\Dao\UserDao;
use FA\Model\User;

class UserDaoTest extends CommonDbTestCase
{
    /**
     * @var UserDao
     */
    protected $dao;

    /**
     * @var User User
     */
    protected $user;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new UserDao($this->db);
        $data = array(
            'id' => '1',
            'email' => 'user@example.com',
            'password_hash' => '$2y$12$pZg9j8DBSIP2R/vfDzTQOeIt5n57r5VigCUl/HH.FrBOadi3YhdPS',
            'last_login' => null
        );

        $this->user = new User($data);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->dao = null;
        parent::tearDown();
    }

    /**
     * @covers FA\Dao\UserDao::find
     */
    public function testFind()
    {
        $user = $this->dao->find($this->user->getId());

        $this->assertNotNull($user);
        $this->assertEquals($this->user, $user);
    }

    /**
     * @covers FA\Dao\UserDao::findByEmail
     */
    public function testFindByEmail()
    {
        $user = $this->dao->findByEmail($this->user->getEmail());

        $this->assertNotNull($user);
        $this->assertEquals($this->user, $user);
    }

    public function testFindByEmailUserNotExist()
    {
        $user = $this->dao->findByEmail('snoop@lion.com');
        $this->assertFalse($user);
    }

    public function testFindAll()
    {
        $result = $this->dao->findAll();
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($this->user, $result[0]);
    }

    public function testRecordLogin()
    {
        $now = new \DateTime('now');
        $this->assertTrue($this->dao->recordLogin($this->user));
        $user = $this->dao->findByEmail($this->user->getEmail());
        $this->assertNotNull($user->getLastLogin());
        $interval = $now->diff($user->getLastLogin());
        $this->assertLessThanOrEqual(3, $interval->s, "last_login wasn't updated within the last 3 seconds.");
    }

    public function testUpdateEmail()
    {
        $newEmail = 'User@Example.ORG';

        $user = $this->dao->findByEmail($this->user->getEmail());
        $this->user->setEmail($newEmail);
        $updatedUser = $this->dao->updateEmail($this->user);

        $this->assertEquals($this->user->getId(), $updatedUser->getId());
        $this->assertEquals($newEmail, $updatedUser->getEmail());
        $this->assertEquals(strtolower($newEmail), $updatedUser->getEmailCanonical());
    }

    public function testChangePassword()
    {
        $user = $this->dao->findByEmail($this->user->getEmail());
        $password = $user->getPasswordHash();

        $newPasswordHash = 'this_is_a_password_h@sh';
        $updatedUser = $this->dao->changePassword($user->getId(), $newPasswordHash);
        $this->assertEquals($newPasswordHash, $updatedUser->getPasswordHash());
    }

    public function testCreateUser()
    {
        $email = 'arthur@example.com';
        $passwordHash = 'Yet another fake password hash';
        $users = count($this->dao->findAll());

        $user = $this->dao->createUser($email, $passwordHash);

        $this->assertEquals($users + 1, count($this->dao->findAll()));
        $this->assertEquals(2, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($passwordHash, $user->getPasswordHash());
    }

    /**
     * This should cover the possibility of a malfunciton in password_hash
     */
    public function testCreateUserFailsWithNullHash()
    {
        $this->setExpectedException('InvalidArgumentException', 'Password hash must not be null');
        $user = $this->dao->createUser('test@example.com', null);
    }
}
