<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Entity;

/**
 * User entity
 */
class User
{
    /**
     * User id
     *
     * @var int
     */
    private $id;

    /**
     * User's first name
     *
     * @var string
     */
    private $firstName;

    /**
     * User's last name
     *
     * @var string
     */
    private $lastName;

    /**
     * User's email address. Doubles as username
     *
     * @var string
     */
    private $email;

    /**
     * User's email normalized to lower case
     *
     * @var string
     */
    private $emailCanonical;

    /**
     * Password hash
     *
     * @var string
     */
    private $passwordHash;

    /**
     * Flickr username
     *
     * @var string
     */
    private $flickrUsername;

    /**
     * Flickr API key
     *
     * @var string
     */
    private $flickrApiKey;

    /**
     * Photographer's homepage, portfolio, Flickr profile, or other.
     *
     * @var string
     */
    private $externalUrl;

    /**
     * DateTime when user last logged in
     *
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * Public constructor
     *
     * @param array $data OPTIONAL user data
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->setFromArray($data);
        }
    }

    /**
     * Sets user properties to array values.  Expects an associative array with
     * keys that match User entity properties
     *
     * @param array $data User data
     */
    public function setFromArray(array $data)
    {

        foreach ($data as $property => $value) {
            $this->__set($property, $value);
        }
    }

    /**
     * Get id
     *
     * @return int User's id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param id $id User's id
     */
    public function setId($id)
    {
        if ($this->id === null) {
            $this->id = (int) $id;
        }
    }

    /**
     * Get firstName
     *
     * @return string firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName User's first name
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get lastName
     *
     * @return string lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName User's last name
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Gets user's full name
     *
     * return string Full name
     */
    public function getFullName()
    {
        $fullName = trim(implode(' ', array($this->firstName, $this->lastName)));

        return empty($fullName) ? null : $fullName;
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
     * @param string $email User's email address
     */
    public function setEmail($email)
    {
        $this->email = $email;

        if ($this->email !== null) {
            $this->emailCanonical = strtolower($this->email);
        }
    }

    /**
     * Get emailCanonical
     *
     * @return string emailCanonical User's email normalized to lower case
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
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
     * @param string $passwordHash Hashed password
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Get flickrUsername
     *
     * @return string flickrUsername
     */
    public function getFlickrUsername()
    {
        return $this->flickrUsername;
    }

    /**
     * Set flickrUsername
     *
     * @param string $flickrUsername the value to set
     */
    public function setFlickrUsername($flickrUsername)
    {
        $this->flickrUsername = $flickrUsername;
    }

    /**
     * Get flickrApiKey
     *
     * @return string flickrApiKey
     */
    public function getFlickrApiKey()
    {
        return $this->flickrApiKey;
    }

    /**
     * Set flickrApiKey
     *
     * @param string $flickrApiKey the value to set
     */
    public function setFlickrApiKey($flickrApiKey)
    {
        $this->flickrApiKey = $flickrApiKey;
    }

    /**
     * Get externalUrl
     *
     * @return string externalUrl
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * Set externalUrl
     *
     * @param string $externalUrl the value to set
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;
    }

    /**
     * Get DateTime when user last logged in
     *
     * @return \DateTime lastLogin
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin When user last logged in
     */
    public function setLastLogin(\DateTime $lastLogin = null)
    {
        $this->lastLogin = $lastLogin;
    }

    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);

        if ($name == 'lastLogin' && is_string($value)) {
            try {
                $value = new \DateTime($value);
            } catch (\Exception $e) {
                $value = null;
            }
        }

        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }
}
