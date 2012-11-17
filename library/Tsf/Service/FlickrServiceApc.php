<?php

namespace Tsf\Service;

use Tsf\Cache\Adapter\Apc;

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
     * @var \Tsf\Service\FlickrInterface
     */
    private $flickr;

    /**
     * @var \Tsf\Cache\Adapter\Apc
     */
    private $apc;

    /**
     * Public constructor
     *
     * @param \Tsf\Service\FlickrInterface $flickr
     * @param \Tsf\Cache\Adapter\Apc       $apc
     */
    public function __construct(FlickrInterface $flickr, Apc $apc)
    {
        $this->flickr = $flickr;
        $this->apc = $apc;
    }

    public function getSizes($photoId)
    {
        $sizes = $this->apc->fetch($photoId);

        if ($sizes === false) {
            $sizes = $this->flickr->getSizes($photoId);
            $this->apc->store($photoId, $sizes);
        }

        return $sizes;
    }

}
