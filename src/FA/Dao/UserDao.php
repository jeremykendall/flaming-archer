<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Dao;

use FA\Model\User;

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
     * Public constructor
     *
     * @param \PDO Database connection
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Find user by id
     *
     * @param  int   $id User id
     * @return array User data
     */
    public function find($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'FA\Model\User');
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Find user by email address
     *
     * @param  string $email User's email address
     * @return array  User record
     */
    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return $data;
        }

        return new User($data);
    }

    /**
     * Updates user email address
     *
     * @param  User $user User to update
     * @return User Updated user
     */
    public function updateEmail(User $user)
    {
        $sql = 'UPDATE users SET email = :email, emailCanonical = :emailCanonical WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'email' => $user->getEmail(),
            'emailCanonical' => $user->getEmailCanonical(),
            'id' => $user->getId()
        ));

        return $this->find($user->getId());
    }

    /**
     * Changes user password
     *
     * @param  int    $id              User id
     * @param  string $newPasswordHash New password hash
     * @return array  Updated user
     */
    public function changePassword($id, $newPasswordHash)
    {
        $sql = 'UPDATE users SET passwordHash = :passwordHash WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('passwordHash' => $newPasswordHash, 'id' => $id));

        return $this->find($id);
    }

    /**
     * Returns all users in the database
     *
     * @return array Users
     */
    public function findAll()
    {
        $result = $this->db->query('SELECT * FROM users')->fetchAll();

        $users = array();

        foreach ($result as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    /**
     * Updates login timestamp
     *
     * @param  User $user User
     * @return bool True on success, false on failure
     */
    public function recordLogin(User $user)
    {
        $sql = "UPDATE users SET lastLogin = datetime('now') WHERE email = :email";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array('email' => $user->getEmail()));
    }

    /**
     * Creates a new user
     *
     * @param  string $email        Email address
     * @param  string $passwordHash Hashed password
     * @return array  User data
     */
    public function createUser($email, $passwordHash)
    {
        if (!$passwordHash) {
            throw new \InvalidArgumentException('Password hash must not be null');
        }

        $sql = 'INSERT INTO users (email, emailCanonical, role, passwordHash) VALUES (:email, :emailCanonical, :role, :passwordHash)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(
            array(
                'email' => $email,
                'emailCanonical' => strtolower($email),
                'passwordHash' => $passwordHash,
                'role' => 'admin'
            )
        );

        return $this->findByEmail($email);
    }
}
