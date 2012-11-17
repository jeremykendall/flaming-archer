<?php

namespace Tsf\Dao;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-11-16 at 20:25:49.
 */
class UserDaoTest extends \CommonDbTestCase
{

    /**
     * @var UserDao
     */
    protected $dao;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dao = new UserDao($this->db);
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
     * @covers Tsf\Dao\UserDao::findByEmail
     * @todo   Implement testFindByEmail().
     */
    public function testFindByEmail()
    {
        $expected = array(
            'id' => '1',
            'email' => 'user@example.com',
            'password_hash' => '$2y$12$pZg9j8DBSIP2R/vfDzTQOeIt5n57r5VigCUl/HH.FrBOadi3YhdPS',
            'last_login' => null
        );
        
        $user = $this->dao->findByEmail('user@example.com');
        
        $this->assertNotNull($user);
        $this->assertEquals($expected, $user);
    }
    
    public function testFindByEmailUserNotExist()
    {
        $user = $this->dao->findByEmail('snoop@lion.com');
        $this->assertFalse($user);
    }

}
