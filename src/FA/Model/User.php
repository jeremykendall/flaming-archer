<?php

namespace FA\Model;

class User extends BaseModel
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
            'lastLogin' => $this->getLastLogin(),
        );

        return $data;
    }
}
