<?php

namespace Tsf\Service;

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
class Images
{
    /**
     * @var \Tsf\Dao\Images 
     */
    private $dao;

    /**
     * @var \Tsf\Flickr 
     */
    private $flickr;
    
    /**
     * Public constructor
     * 
     * @param \Tsf\Dao\Images $dao
     * @param \Tsf\Flickr $flickr 
     */
    public function __construct(\Tsf\Dao\Images $dao, \Tsf\Flickr $flickr)
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
}
