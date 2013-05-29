<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Authentication\Adapter;

use Fa\Dao\UserDao;
use Phpass\Hash as Hasher;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/**
 * Database auththentication adapter
 */
class DbAdapter implements AdapterInterface
{

    /**
     * User Dao
     *
     * @var Fa\Dao\UserDao
     */
    private $dao;

    /**
     * Password hasher
     *
     * @var Phpass\Hash
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
     * @param UserDao $dao    User Dao
     * @param Hasher  $hasher Password hasher
     */
    public function __construct(UserDao $dao, Hasher $hasher)
    {
        $this->dao = $dao;
        $this->hasher = $hasher;
    }

    /**
     * Sets user email and password
     *
     * @param string $email
     * @param string $password
     */
    public function setCredentials($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Performs authentication
     *
     * @return Result Authentication result
     */
    public function authenticate()
    {
        $user = $this->dao->findByEmailCanonical($this->email);

        if ($user === false) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, array(), array('Invalid username or password provided'));
        }

        if ($this->hasher->checkPassword($this->password, $user->getPasswordHash())) {
            $user->setPasswordHash(null);
            $user->setFlickrApiKey(null);
            $this->dao->recordLogin($user->getEmail());

            return new Result(Result::SUCCESS, $user, array());
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, array(), array('Invalid username or password provided'));
        }
    }

}
