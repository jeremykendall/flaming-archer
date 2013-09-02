<?php

namespace FA\Tests\Dao;

use FA\Dao\UserDao;

class UserDaoTest extends CommonDbTestCase
{
    /**
     * @var UserDao
     */
    protected $dao;

    /**
     * @var array User
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
        $this->user = array(
            'id' => '1',
            'email' => 'user@example.com',
            'password_hash' => '$2y$12$pZg9j8DBSIP2R/vfDzTQOeIt5n57r5VigCUl/HH.FrBOadi3YhdPS',
            'last_login' => null
        );
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
        $user = $this->dao->find($this->user['id']);

        $this->assertNotNull($user);
        $this->assertEquals($this->user, $user);
    }

    /**
     * @covers FA\Dao\UserDao::findByEmail
     */
    public function testFindByEmail()
    {
        $user = $this->dao->findByEmail($this->user['email']);

        $this->assertNotNull($user);
        $this->assertEquals($this->user, $user);
    }

    public function testFindByEmailUserNotExist()
    {
        $user = $this->dao->findByEmail('snoop@lion.com');
        $this->assertFAlse($user);
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
        $email = $this->user['email'];
        $now = new \DateTime('now');
        $this->assertTrue($this->dao->recordLogin($email));
        $user = $this->dao->findByEmail($email);
        $this->assertNotNull($user['last_login']);
        $last_login = new \DateTime($user['last_login']);
        $interval = $now->diff($last_login);
        $this->assertLessThanOrEqual(3, $interval->s, "last_login wasn't updated within the last 3 seconds.");
    }

    public function testUpdateEmail()
    {
        $newEmail = 'user@example.org';

        $user = $this->dao->findByEmail($this->user['email']);
        $updatedUser = $this->dao->updateEmail($user['id'], $newEmail);

        $this->assertEquals($user['id'], $updatedUser['id']);
        $this->assertEquals($newEmail, $updatedUser['email']);
    }

    public function testChangePassword()
    {
        $user = $this->dao->findByEmail($this->user['email']);
        $password = $user['password_hash'];

        $newPasswordHash = 'this_is_a_password_h@sh';
        $updatedUser = $this->dao->changePassword($user['id'], $newPasswordHash);
        $this->assertEquals($newPasswordHash, $updatedUser['password_hash']);
    }

    public function testCreateUser()
    {
        $email = 'arthur@example.com';
        $passwordHash = 'Yet another fake password hash';
        $users = count($this->dao->findAll());

        $user = $this->dao->createUser($email, $passwordHash);

        $this->assertEquals($users + 1, count($this->dao->findAll()));
        $this->assertEquals(2, $user['id']);
        $this->assertEquals($email, $user['email']);
        $this->assertEquals($passwordHash, $user['password_hash']);
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
