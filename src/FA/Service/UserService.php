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
     * @param array  $user  User data
     * @param string $email New email address
     *
     * @return array User data
     */
    public function updateEmail(array $user, $email)
    {
        $user = $this->dao->updateEmail($user['id'], $email);
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

        $authResult = $this->authenticate($email, $current);

        if (!$authResult->isValid()) {
            throw new \Exception('Your current password is incorrect.');
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);

        $user = $this->dao->findByEmail($email);
        $user = $this->dao->changePassword($user['id'], $newHash);
        $this->authenticate($email, $new);

        if (!$authResult->isValid()) {
            throw new \Exception('Your password was changed but there was an issue reauthenticating. PLease log out and back in with your new password.');
        }

        unset($user['password_hash']);

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
