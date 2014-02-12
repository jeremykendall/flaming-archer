<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Service;

use FA\Dao\UserDao;
use FA\Model\User;
use Zend\Authentication\AuthenticationService;

/**
 * Handles user logic
 */
class UserService
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
     * Public constructor
     *
     * @param UserDao               $dao  UserDao
     * @param AuthenticationService $auth AuthenticationService
     */
    public function __construct(UserDao $dao, AuthenticationService $auth)
    {
        $this->dao = $dao;
        $this->auth = $auth;
    }

    /**
     * Updates user's email address and updates auth storage with
     * updated user data
     *
     * @param  User $user User data
     * @return User Updated user
     */
    public function updateEmail(User $user)
    {
        $user = $this->dao->updateEmail($user);
        $this->auth->clearIdentity();
        $this->auth->getStorage()->write($user);

        return $user;
    }

    /**
     * Changes user password
     *
     * @param  string    $current Current password
     * @param  string    $new     New password
     * @param  string    $confirm Confirm new password
     * @throws Exception
     * @return array     User array
     */
    public function changePassword($email, $current, $new, $confirm)
    {
        if (!$new || !$confirm) {
            throw new \InvalidArgumentException('New and confirm password are both required.');
        }

        if ($new !== $confirm) {
            throw new \InvalidArgumentException('New and confirm passwords must match.');
        }

        if (strlen($new) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters in length.');
        }

        $authResult = $this->checkPassword($email, $current);

        if (!$authResult->isValid()) {
            throw new \Exception('Your current password is incorrect.');
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);

        $user = $this->dao->findByEmail($email);
        $user = $this->dao->changePassword($user->getId(), $newHash);
        $this->authenticate($email, $new);

        if (!$authResult->isValid()) {
            throw new \Exception('Your password was changed but there was an issue reauthenticating. PLease log out and back in with your new password.');
        }

        return $user;
    }

    /**
     * Creates a new user
     *
     * @param  string $email    Email address
     * @param  string $password Password
     * @param  string $confirm  Password confirmation
     * @return array  User data
     */
    public function createUser($email, $password, $confirm)
    {
        if (!$password || !$confirm) {
            throw new \InvalidArgumentException('Password and confirm password are both required.');
        }

        if ($password !== $confirm) {
            throw new \InvalidArgumentException('Passwords must match.');
        }

        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters in length.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $user = $this->dao->createUser($email, $passwordHash);

        return $user;
    }

    /**
     * Finds currently logged in user
     *
     * @return array User data
     */
    public function getLoggedInUser()
    {
        if ($this->auth->hasIdentity()) {
            return $this->auth->getIdentity();
        }
    }

    /**
     * Checks username and password for validity
     *
     * @param  string                                         $email    User email
     * @param  string                                         $password User password
     * @return Zend\Authentication\Result
     * @throws Zend\Authentication\Exception\RuntimeException
     */
    public function checkPassword($email, $password)
    {
        $adapter = $this->auth->getAdapter();
        $adapter->setCredentials($email, $password);

        return $adapter->authenticate();
    }

    /**
     * Authenticates user
     *
     * @param  string                                         $email    User email
     * @param  string                                         $password User password
     * @return Zend\Authentication\Result
     * @throws Zend\Authentication\Exception\RuntimeException
     */
    public function authenticate($email, $password)
    {
        $adapter = $this->auth->getAdapter();
        $adapter->setCredentials($email, $password);

        return $this->auth->authenticate();
    }

    /**
     * Clears identity
     */
    public function clearIdentity()
    {
        $this->auth->clearIdentity();
    }
}
