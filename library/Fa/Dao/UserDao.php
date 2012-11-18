<?php

namespace Fa\Dao;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserDao
 *
 * @author jkendall
 */
class UserDao
{

    /**
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
     * Finds user by email address
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

}
