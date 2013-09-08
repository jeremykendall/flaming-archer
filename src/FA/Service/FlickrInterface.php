<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Service;

/**
 * Flickr service interface
 *
 * Abstracts calls to the Flickr API
 */
interface FlickrInterface
{
    /**
     * Finds photo information on Flickr
     *
     * @param  int   $photoId Flickr photo id
     * @return array Photo information
     */
    public function find($photoId);
}
