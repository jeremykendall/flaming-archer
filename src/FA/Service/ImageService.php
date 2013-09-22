<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Service;

use FA\Dao\ImageDao;
use FA\Model\Photo\Photo;
use FA\Service\FlickrInterface;

/**
 * Image service abstracts application requests for image data
 */
class ImageService
{
    /**
     * Image Dao
     *
     * @var ImageDao
     */
    private $dao;

    /**
     * Instance of object honoring the FlickrInterface
     *
     * @var FlickrInterface
     */
    private $flickr;

    /**
     * Public constructor
     *
     * @param ImageDao        $dao
     * @param FlickrInterface $flickr
     */
    public function __construct(ImageDao $dao, FlickrInterface $flickr)
    {
        $this->dao = $dao;
        $this->flickr = $flickr;
    }

    /**
     * Find image by day
     *
     * @param  int   $day Project day (1-366)
     * @return Photo Photo identified by day
     */
    public function find($day)
    {
        $photo = $this->dao->find($day);

        if (!$photo) {
            return null;
        }

        $photo = $this->flickr->find($photo);

        return $photo;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  int   $offset           Page offset
     * @param  int   $itemCountPerPage Number of items per page
     * @return Photo[] Page items
     */
    public function findPage($offset, $itemCountPerPage)
    {
        $photos = $this->dao->findPage($offset, $itemCountPerPage);
        $result = array();

        foreach ($photos as $photo) {
            $result[] = $this->flickr->find($photo);
        }

        return $result;
    }

    /**
     * Finds next day's image
     *
     * @param  int $currentDay Current day
     * @return int Day after current day
     */
    public function findNextImage($currentDay)
    {
        return $this->dao->findNextImage($currentDay);
    }

    /**
     * Finds previous day's image
     *
     * @param  int $currentDay Current day
     * @return int Day before current day
     */
    public function findPreviousImage($currentDay)
    {
        return $this->dao->findPreviousImage($currentDay);
    }

    /**
     * Find all images
     *
     * @return Photo[] All images
     */
    public function findAll()
    {
        $photos = $this->dao->findAll();
        $result = array();

        foreach ($photos as $photo) {
            $result[] = $this->flickr->find($photo);
        }

        return $result;
    }

    /**
     * Save new image
     *
     * @param  Photo $photo Photo to save
     * @return bool  True on success, false on failure
     */
    public function save(Photo $photo)
    {
        return $this->dao->save($photo);
    }

    /**
     * Delete image
     *
     * @param  Photo $photo Photo to delete
     * @return bool  True on success, false on failure
     */
    public function delete(Photo $photo)
    {
        return $this->dao->delete($photo);
    }

    /**
     * Determines what project day it is by diffing project start and current date
     *
     * @param DateTime $today Today's date OPTIONAL
     * @return int Project day (n of 365)
     */
    public function getProjectDay(\DateTime $today = null)
    {
        if ($today === null) {
            $today = new \DateTime();
        }

        $today->setTime(0, 0, 0);

        $firstImage = $this->dao->findFirstImage();

        if (false === $firstImage) {
            return 1;
        }

        $firstPostedDate = $firstImage->getPosted();
        $firstPostedDate->setTime(0, 0, 0);

        $interval = $today->diff($firstPostedDate, true);
        $daysElapsed = $interval->format('%a');

        return $daysElapsed + 1;
    }

    /**
     * Gets a count of all images in database
     *
     * @return int Count of images
     */
    public function countImages()
    {
        return $this->dao->countImages();
    }
}
