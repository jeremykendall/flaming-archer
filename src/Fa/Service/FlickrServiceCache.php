<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Service;

use \Zend\Cache\Storage\Adapter\AbstractAdapter;

/**
 * Flickr service cache
 *
 * Abstracts calls to the Flickr API and caches results
 */
class FlickrServiceCache implements FlickrInterface
{

    /**
     * Instance of object honoring the \Fa\Service\FlickrInterface
     *
     * @var \Fa\Service\FlickrInterface
     */
    private $flickr;

    /**
     * Abstract caching adapter
     *
     * @var \Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    private $adapter;

    /**
     * Public constructor
     *
     * @param \Fa\Service\FlickrInterface                 $flickr
     * @param \Zend\Cache\Storage\Adapter\AbstractAdapter $adapter
     */
    public function __construct(FlickrInterface $flickr, AbstractAdapter $adapter)
    {
        $this->flickr = $flickr;
        $this->adapter = $adapter;
    }

    /**
     * Returns sizes array for photo identified by Flickr photo id
     *
     * @param  int   $photoId
     * @return array Array of photo size information
     */
    public function getSizes($photoId)
    {
        $sizes = $this->adapter->getItem($photoId);

        if (is_null($sizes)) {
            $sizes = $this->flickr->getSizes($photoId);
            $this->adapter->addItem($photoId, $sizes);
        }

        return $sizes;
    }

}
