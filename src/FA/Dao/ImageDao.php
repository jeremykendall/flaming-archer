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
 * Image Dao
 */
class ImageDao
{
    /**
     * Database connection
     *
     * @var \PDO
     */
    private $db;

    /**
     * Public constructor
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Find image by day
     *
     * @param  int   $day Project day (1-366)
     * @return array Image data
     */
    public function find($day)
    {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE day = :day");
        $stmt->bindValue(':day', $day);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  int   $offset           Page offset
     * @param  int   $itemCountPerPage Number of items per page
     * @return array Page items
     */
    public function findPage($offset, $itemCountPerPage)
    {
        $sql = 'SELECT * FROM images ORDER BY day DESC LIMIT :itemCountPerPage OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':itemCountPerPage', $itemCountPerPage);
        $stmt->bindValue(':offset', $offset);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Finds next day's image
     *
     * @param int $currentDay Current day
     * @return int Day after current day
     */
    public function findNextImage($currentDay)
    {
        $sql = "SELECT day FROM images WHERE day > :currentDay ORDER BY day LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':currentDay', $currentDay);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Finds previous day's image
     *
     * @param int $currentDay Current day
     * @return int Day before current day
     */
    public function findPreviousImage($currentDay)
    {
        $sql = "SELECT day FROM images WHERE day < :currentDay ORDER BY day DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':currentDay', $currentDay);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Find all images
     *
     * @return array All images
     */
    public function findAll()
    {
        return $this->db->query("SELECT * FROM images ORDER BY day DESC")->fetchAll();
    }

    /**
     * Save new image
     *
     * @param  array $data Array containing 'day' and 'photo_id' keys
     * @return bool  True on success, false on failure
     */
    public function save(array $data)
    {
        $sql = 'INSERT INTO images (day, photo_id) VALUES (:day, :photo_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':day', $data['day']);
        $stmt->bindValue(':photo_id', $data['photo_id']);

        return $stmt->execute();
    }

    /**
     * Delete image
     *
     * @param  int  $day Project day (1-366)
     * @return bool True on success, false on failure
     */
    public function delete($day)
    {
        $sql = 'DELETE FROM images WHERE day = :day';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':day', $day);

        return $stmt->execute();
    }

    /**
     * Counts images in project
     *
     * @return int Count of images in project
     */
    public function countImages()
    {
        $sql = 'SELECT COUNT(id) FROM images';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Finds the first image (oldest date) added to the project
     *
     * @return array First image added to the project
     */
    public function findFirstImage()
    {
        $sql = 'SELECT * FROM images ORDER BY posted ASC LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }
}
