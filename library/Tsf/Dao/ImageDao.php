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
class ImageDao
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
    
    public function save(array $data)
    {
        $sql = 'INSERT INTO images (day, photo_id) VALUES (:day, :photo_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':day', $data['day'], \PDO::PARAM_INT);
        $stmt->bindValue(':photo_id', $data['photo_id'], \PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function delete($day)
    {
        $sql = 'DELETE FROM images WHERE day = :day';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':day', $day, \PDO::PARAM_INT);
        return $stmt->execute();
    }

}
