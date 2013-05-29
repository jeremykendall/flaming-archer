<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Dao;

use \DateTime;
use Fa\Entity\User;
use Fa\Exception\UserLimitException;

/**
 * User Dao
 */
class UserDao
{
    /**
     * Database connection
     *
     * @var \PDO
     */
    protected $db;

    /**
     * DateTime format
     *
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';

    /**
     * Public constructor
     *
     * @param \PDO Database connection
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Finds user by id
     *
     * @param  int   $id User's id
     * @return mixed The user identified by id or false if user not found
     */
    public function find($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_INTO, new User());
        $result = $stmt->fetch();

        return $result;
    }

    /**
     * Find user by canonical email address
     *
     * @param  string $email User's email address
     * @return mixed  User is query successful, false otherwise
     */
    public function findByEmailCanonical($email)
    {
        $sql = 'SELECT * FROM users WHERE emailCanonical = :emailCanonical';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':emailCanonical', strtolower($email), \PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_INTO, new User());
        $result = $stmt->fetch();

        return $result;
    }

    /**
     * Returns all users in the database
     *
     * @return array Array of User objects
     */
    public function findAll()
    {
        $sql = 'SELECT * FROM users';
        $result = $this->db->query($sql)->fetchAll();

        $userArray = array();

        foreach ($result as $data) {
            $userArray[] = new User($data);
        }

        return $userArray;
    }

    /**
     * Saves a user
     *
     * @param  User $user
     * @return User Saved user
     */
    public function save(User $user)
    {
        if ($this->find($user->getId())) {
            return $this->update($user);
        } else {
            return $this->insert($user);
        }
    }

    /**
     * Inserts a User
     *
     * @param  User               $user User entity
     * @return User               Persited user entity
     * @throws UserLimitException If an attempt to persist a second user occurs
     */
    private function insert(User $user)
    {
        if ($this->userExists()) {
            throw new UserLimitException('No more than one user is allowed.');
        }

        $sql = "INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `emailCanonical`, `flickrUsername`, `flickrApiKey`, `externalUrl`, `passwordHash`, `lastLogin`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $statement = $this->db->prepare($sql);

        $params = array(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getEmailCanonical(),
            $user->getFlickrUsername(),
            $user->getFlickrApiKey(),
            $user->getExternalUrl(),
            $user->getPasswordHash(),
            $this->formatTimestamp($user->getLastLogin()),
        );

        $statement->execute($params);

        return $this->find($this->db->lastInsertId());
    }

    /**
     * Updates a User
     *
     * @param User User entity
     * @return User Updated user entity
     */
    private function update(User $user)
    {
        $sql = "UPDATE `users` SET "
            . "`firstName` = ?, "
            . "`lastName` = ?, "
            . "`email` = ?, "
            . "`emailCanonical` = ?, "
            . "`flickrUsername` = ?, "
            . "`flickrApiKey` = ?, "
            . "`externalUrl` = ?, "
            . "`passwordHash` = ?, "
            . "`lastLogin` = ? "
            . "WHERE `id` = ?";

        $statement = $this->db->prepare($sql);

        $params = array(
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getEmailCanonical(),
            $user->getFlickrUsername(),
            $user->getFlickrApiKey(),
            $user->getExternalUrl(),
            $user->getPasswordHash(),
            $this->formatTimestamp($user->getLastLogin()),
            $user->getId(),
        );

        $statement->execute($params);

        return $this->find($user->getId());
    }

    /**
     * Updates login timestamp
     *
     * @param  string     $email User's email address
     * @throws \Exception If user doesn't exist
     * @return User       The updated User
     */
    public function recordLogin($email)
    {
        $user = $this->findByEmailCanonical($email);

        if (!$user) {
            throw new \InvalidArgumentException($email . ' does not exist');
        }

        $user->setLastLogin(new DateTime('now'));

        return $this->save($user);
    }

    public function formatTimestamp(DateTime $timestamp = null)
    {
        return ($timestamp) ? $timestamp->format($this->format) : null;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function userExists()
    {
        $sql = 'SELECT COUNT(`id`) FROM `users`';
        $result = $this->db->query($sql)->fetch(\PDO::FETCH_COLUMN);

        return ($result) ? true : false;
    }

    /**
     * Gets db
     *
     * @return \PDO Database instance
     */
    public function getDb()
    {
        return $this->db;
    }
}
