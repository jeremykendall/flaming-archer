<?php

namespace Fa\Authentication\Adapter;

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
class DbAdapter implements \Zend\Authentication\Adapter\AdapterInterface
{

    /**
     * Database connection
     *
     * @var \Fa\Dao\UserDao
     */
    private $dao;

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
     * @param \Fa\Dao\UserDao $dao    User Dao
     * @param \Phpass\Hash    $hasher Password hasher
     */
    public function __construct(\Fa\Dao\UserDao $dao, \Phpass\Hash $hasher)
    {
        $this->dao = $dao;
        $this->hasher = $hasher;
    }

    public function setCredentials($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function authenticate()
    {
        $user = $this->dao->findByEmail($this->email);

        if ($user === false) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, array(), array('Invalid username or password provided'));
        }

        if ($this->hasher->checkPassword($this->password, $user['password_hash'])) {
            unset($user['password_hash']);

            return new Result(Result::SUCCESS, $user, array());
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, array(), array('Invalid username or password provided'));
        }
    }

}
