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
 * FlickrApc class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class FlickrServiceApc implements FlickrInterface
{

    /**
     * @var Tsf\Service\FlickrInterface
     */
    private $flickr;

    public function __construct(FlickrInterface $flickr)
    {
        $this->flickr = $flickr;
    }

    public function getSizes($photoId)
    {
        if (apc_fetch($photoId) === false) {
            $sizes = $this->flickr->getSizes($photoId);
            apc_store($photoId, $sizes);
        } else {
            $sizes = apc_fetch($photoId);
        }
        
        return $sizes;
    }

}