<?php

namespace Tsf\Service;

use \Zend\Cache\Storage\Adapter\AbstractAdapter;

/**
 * --- Library
 *
 * @category
 * @package
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * FlickrServiceCache class
 *
 * @category
 * @package
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class FlickrServiceCache implements FlickrInterface
{

    /**
     * @var \Tsf\Service\FlickrInterface
     */
    private $flickr;

    /**
     * @var \Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    private $adapter;

    /**
     * Public constructor
     *
     * @param \Tsf\Service\FlickrInterface                $flickr
     * @param \Zend\Cache\Storage\Adapter\AbstractAdapter $adapter
     */
    public function __construct(FlickrInterface $flickr, AbstractAdapter $adapter)
    {
        $this->flickr = $flickr;
        $this->adapter = $adapter;
    }

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
