<?php

namespace Tsf\Dao;

/**
 * --- Library
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * Image class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class Images
{

    /**
     * @var \PDO
     */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function find($day)
    {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE day = :day");
        $stmt->bindValue(':day', $day, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findAll()
    {
        return $this->db->query("SELECT * FROM images ORDER BY day DESC")->fetchAll();
    }

}
