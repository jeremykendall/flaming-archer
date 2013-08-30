<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Authentication\Adapter;

use FA\Dao\UserDao;
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
     * @var UserDao
     */
    private $dao;

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
     * @param UserDao $dao User Dao
     */
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
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
        $user = $this->dao->findByEmail($this->email);

        if ($user === false) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, array(), array('Invalid username or password provided'));
        }

        if (password_verify($this->password, $user['password_hash'])) {
            unset($user['password_hash']);
            $this->dao->recordLogin($user['email']);

            return new Result(Result::SUCCESS, $user, array());
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, array(), array('Invalid username or password provided'));
        }
    }
}
