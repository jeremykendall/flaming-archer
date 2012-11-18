<?php

namespace Fa\Service;

/**
 * --- Library
 *
 * @category
 * @package
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * Images class
 *
 * @category
 * @package
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class ImageService
{
    /**
     * @var \Fa\Dao\ImageDao
     */
    private $dao;

    /**
     * @var \Fa\Service\FlickrInterface
     */
    private $flickr;

    /**
     * Public constructor
     *
     * @param \Fa\Dao\ImageDao            $dao
     * @param \Fa\Service\FlickrInterface $flickr
     */
    public function __construct(\Fa\Dao\ImageDao $dao, \Fa\Service\FlickrInterface $flickr)
    {
        $this->dao = $dao;
        $this->flickr = $flickr;
    }

    public function find($day)
    {
        $image = $this->dao->find($day);

        if (!$image) {
            return null;
        }

        $sizes = $this->flickr->getSizes($image['photo_id']);

        return array_merge($image, $sizes);
    }

    public function findAll()
    {
        $images = $this->dao->findAll();
        $result = array();

        foreach ($images as $image) {
            $result[] = array_merge($image, $this->flickr->getSizes($image['photo_id']));
        }

        return $result;
    }

    public function save(array $data)
    {
        return $this->dao->save($data);
    }

    public function delete($day)
    {
        return $this->dao->delete($day);
    }
}
