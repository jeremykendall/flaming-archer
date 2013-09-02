<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Dao;

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

        return $stmt->fetch();
    }

    /**
     * Updates user email address
     *
     * @param  int    $id    User id
     * @param  string $email New email address
     * @return array  Updated user
     */
    public function updateEmail($id, $email)
    {
        $sql = 'UPDATE users SET email = :email WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('email' => $email, 'id' => $id));

        return $this->find($id);
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
        $sql = 'UPDATE users SET password_hash = :password_hash WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('password_hash' => $newPasswordHash, 'id' => $id));

        return $this->find($id);
    }

    /**
     * Returns all users in the database
     *
     * @return array Users
     */
    public function findAll()
    {
        return $this->db->query('SELECT * FROM users')->fetchAll();
    }

    /**
     * Updates login timestamp
     *
     * @param  string $email User's email address
     * @return bool   True on success, false on failure
     */
    public function recordLogin($email)
    {
        $sql = "UPDATE users SET last_login = datetime('now') WHERE email = :email";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array('email' => $email));
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

        $sql = 'INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('email' => $email, 'password_hash' => $passwordHash));

        return $this->findByEmail($email);
    }
}
