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
use Zend\Cache\Storage\StorageInterface as CacheStorage;

/**
 * Flickr service cache
 *
 * Abstracts calls to the Flickr API and caches results
 */
class FlickrServiceCache implements FlickrInterface
{
    /**
     * Instance of object honoring the FA\Service\FlickrInterface
     *
     * @var FlickrInterface
     */
    private $flickr;

    /**
     * ZF CacheStorage
     *
     * @var CacheStorage
     */
    protected $cache;

    /**
     * Public constructor
     *
     * @param FlickrInterface $flickr
     * @param CacheStorage    $cache
     */
    public function __construct(FlickrInterface $flickr, CacheStorage $cache)
    {
        $this->flickr = $flickr;
        $this->cache = $cache;
    }

    /**
     * Finds photo on Flickr
     *
     * @param  int   $photoId Flickr photo id
     * @return array Photo data from Flickr
     */
    public function find(Photo $photoId)
    {
        $photo = $this->cache->getItem($photoId);

        if (is_null($photo)) {
            $photo = $this->flickr->find($photoId);
            $this->cache->addItem($photoId, $photo);
        }

        return $photo;
    }
}
