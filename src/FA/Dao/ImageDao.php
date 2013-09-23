<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Dao;

use FA\Model\Photo\Photo;

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
     * @return Photo Photo identified by day
     */
    public function find($day)
    {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE day = :day");
        $stmt->bindValue(':day', $day);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'FA\Model\Photo\Photo');
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  int     $offset           Page offset
     * @param  int     $itemCountPerPage Number of items per page
     * @return Photo[] Page items
     */
    public function findPage($offset, $itemCountPerPage)
    {
        $sql = 'SELECT * FROM images ORDER BY day DESC LIMIT :itemCountPerPage OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':itemCountPerPage', $itemCountPerPage);
        $stmt->bindValue(':offset', $offset);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, 'FA\Model\Photo\Photo');
    }

    /**
     * Finds next day's image
     *
     * @param  int $currentDay Current day
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
     * @param  int $currentDay Current day
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
     * @return Photo[] All images
     */
    public function findAll()
    {
        return $this->db
            ->query("SELECT * FROM images ORDER BY day DESC")
            ->fetchAll(\PDO::FETCH_CLASS, 'FA\Model\Photo\Photo');
    }

    /**
     * Save new image
     *
     * @param  Photo $photo Photo to save
     * @return bool  True on success, false on failure
     */
    public function save(Photo $photo)
    {
        $sql = 'INSERT INTO images (day, photoId) VALUES (:day, :photoId)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':day', $photo->getDay());
        $stmt->bindValue(':photoId', $photo->getPhotoId());

        return $stmt->execute();
    }

    /**
     * Delete image
     *
     * @param  Photo $photo Photo to delete
     * @return bool  True on success, false on failure
     */
    public function delete(Photo $photo)
    {
        $sql = 'DELETE FROM images WHERE day = :day';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':day', $photo->getDay());

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
     * @return Photo First photo added to project
     */
    public function findFirstImage()
    {
        $sql = 'SELECT * FROM images ORDER BY posted ASC LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'FA\Model\Photo\Photo');
        $stmt->execute();

        return $stmt->fetch();
    }
}
