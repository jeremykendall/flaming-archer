<?php

namespace Tsf\Authentication\Adapter;

use \Zend\Authentication\Result;

/**
 * Flaming Archer Library
 * 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */

/**
 * Database auth adapter
 * 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class DbAdapter implements \Zend\Authentication\Adapter\AdapterInterface {

    /**
     * Database connection
     *
     * @var \PDO
     */
    private $db;
    
    /**
     * Password hasher
     *
     * @var \Phpass\Hash
     */
    private $hasher;

    /**
     * User email address
     *
     * @var string Email address
     */
    private $email;

    /**
     * User password
     *
     * @var string Password
     */
    private $password;

    /**
     * Public constructor
     * 
     * @param \PDO $db Database connection
     * @param \Phpass\Hash $hasher Password hasher
     */
    public function __construct(\PDO $db, \Phpass\Hash $hasher) {
        $this->db = $db;
        $this->hasher = $hasher;
    }
    
    public function setCredentials($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function authenticate() {
        try {
            $sql = 'SELECT email, password_hash FROM users WHERE email = :email';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $this->email, \PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            throw new \Zend\Authentication\Exception\RuntimeException($e->getMessage());
        }

        if ($this->hasher->checkPassword($this->password, $user['password_hash'])) {
            unset($user['password_hash']);
            return new Result(Result::SUCCESS, $user, array());
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, array(), array('Invalid username or password provided'));
        }
    }

}
