<?php

namespace Tsf\Service;

interface FlickrInterface
{
    /**
     * Returns sizes array for photo identified by Flickr photo id
     * 
     * @param int $photoId Flickr photo id
     * @return array Array of photo size information 
     */
    public function getSizes($photoId);
}