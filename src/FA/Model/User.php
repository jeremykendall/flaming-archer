<?php

namespace FA\Model;

use JeremyKendall\Slim\Auth\IdentityInterface;

class User extends BaseModel implements IdentityInterface
{
    /**
     * @var string User email
     */
    protected $email;

    /**
     * @var string User email lower case
     */
    protected $emailCanonical;

    /**
     * @var string password hash
     */
    protected $passwordHash;

    /**
     * @var string user role
     */
    protected $role;

    /**
     * @var DateTime User's last login time
     */
    protected $lastLogin;

    /**
     * Get email
     *
     * @return string email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        $this->setEmailCanonical(strtolower($email));
    }

    /**
     * Get emailCanonical
     *
     * @return string emailCanonical
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Set emailCanonical
     *
     * @param string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
    }

    /**
     * Get passwordHash
     *
     * @return string passwordHash
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * Set passwordHash
     *
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Get role
     *
     * @return string User role
     */
    public function getRole()
    {
        return $this->role;
    }
    
    /**
     * Set role
     *
     * @param string $role User role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
    /**
     * Get lastLogin
     *
     * @return DateTime User's last login time
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set lastLogin
     *
     * @param DateTime $lastLogin the value to set
     */
    public function setLastLogin(\DateTime $lastLogin = null)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Returns array representation of User
     *
     * @return array User data
     */
    public function toArray()
    {
        $data = array(
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'emailCanonical' => $this->getEmailCanonical(),
            'passwordHash' => $this->getPasswordHash(),
            'role' => $this->getRole(),
            'lastLogin' => $this->getLastLogin(),
        );

        return $data;
    }
}
