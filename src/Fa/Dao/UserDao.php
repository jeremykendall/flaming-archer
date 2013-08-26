<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Dao;

/**
 * User Dao
 */
class UserDao
{
    /**
     * Database connection
     *
     * @var \PDO
     */
    protected $db;

    /**
     * Public constructor
     *
     * @param \PDO Database connection
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Find user by email address
     *
     * @param  string $email User's email address
     * @return array  User record
     */
    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        return $user;
    }

    /**
     * Returns all users in the database
     *
     * @return array Users
     */
    public function findAll()
    {
        $sql = 'SELECT * FROM users';

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Updates login timestamp
     *
     * @param  string $email User's email address
     * @return bool   True on success, false on failure
     */
    public function recordLogin($email)
    {
        $sql = "UPDATE users SET last_login = datetime('now') WHERE email = :email";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array('email' => $email));
    }
}
