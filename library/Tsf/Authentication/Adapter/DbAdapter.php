<?php

namespace Tsf\Authentication\Adapter;

use \Zend\Authentication\Result;

/**
 * --- Library
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * Db class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class Db implements \Zend\Authentication\Adapter\AdapterInterface
{

    private $db;
    private $email;
    private $password;

    public function __construct(\PDO $db, $email, $password)
    {
        $this->db = $db;
        $this->email = $email;
        $this->password = $password;
    }

    public function authenticate()
    {
        try {
            $sql = 'SELECT email, salt, password_hash FROM users WHERE email = :email';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $this->email, \PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            throw new \Zend\Authentication\Exception\RuntimeException($e->getMessage());
        }
        
        $hash = crypt($this->password, $user['salt']);

        if ($hash == $user['password_hash']) {
            unset($user['salt']);
            unset($user['password_hash']);
            return new Result(Result::SUCCESS, $user, array());
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, array(), array('Invalid username or password provided'));
        }
    }

}
