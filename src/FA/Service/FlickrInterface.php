<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Service;

use FA\Model\Photo\Photo;

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
     * @param  Photo Photo object without Flickr data
     * @return Photo Photo object populated with Flickr data
     */
    public function find(Photo $photo);

    /**
     * Finds photo information on Flickr
     *
     * @param  Photo[] Array of Photo objects without Flickr data
     * @return Photo[] Array of Photo objects with Flickr data
     */
    public function findPhotos(array $photos);

    /**
     * Searches Flickr for photos based on provided options
     *
     * @see http://www.flickr.com/services/api/flickr.photos.search.html flickr.photos.search docs
     * @param  array $options Search options
     * @return array Search results
     */
    public function search(array $options);
}
