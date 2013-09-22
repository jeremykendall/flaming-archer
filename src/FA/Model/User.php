<?php

namespace FA\Model;

class User implements \Serializable
{
    /**
     * @var int User id
     */
    protected $id;

    /**
     * @var string User email
     */
    protected $email;

    /**
     * @var string User email lower case
     */
    protected $emailCanonical;

    /**
     * @var string password
     */
    protected $password_hash;

    /**
     * @var DateTime User's last login time
     */
    protected $last_login;

    /**
     * Public constructor
     *
     * @param array $data OPTIONAL user data
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }


    /**
     * Get id
     *
     * @return int id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * Get password_hash
     *
     * @return string password_hash
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * Set password_hash
     *
     * @param string $password_hash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->password_hash = $passwordHash;
    }

    /**
     * Get last_login
     *
     * @return DateTime User's last login time
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * Set last_login
     *
     * @param DateTime $last_login the value to set
     */
    public function setLastLogin(\DateTime $lastLogin = null)
    {
        $this->last_login = $lastLogin;
    }

    /**
     * Sets properties from array
     *
     * @param array $data User data
     */
    public function fromArray(array $data)
    {
        foreach ($data as $property => $value) {
            if ($property == 'last_login' && $value != null) {
                if (!$value instanceof \DateTime) {
                    $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                }
            }

            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
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
            'password_hash' => $this->getPasswordHash(),
            'last_login' => $this->getLastLogin(),
        );

        return $data;
    }

    /**
     * Serializes User
     *
     * @return string Serialized user
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserializes user
     *
     * @param string $data Serialized User data
     */
    public function unserialize($data)
    {
        $this->fromArray(unserialize($data));
    }
}
