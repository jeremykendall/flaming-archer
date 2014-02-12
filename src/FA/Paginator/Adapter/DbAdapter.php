<?php

namespace FA\Paginator\Adapter;

use FA\Service\ImageService;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Cache\Storage\StorageInterface as CacheStorage;

class DbAdapter implements AdapterInterface
{
    /**
     * The cache key prefix used to namespace Paginator results in the cache
     */
    const CACHE_KEY_PREFIX = 'FA_PAGE_';

    /**
     * @var FA\Service\ImageService
     */
    protected $service;

    /**
     * @var int Total image count
     */
    protected $count;

    /**
     * @var CacheStorage
     */
    protected $cache;

    /**
     * Enable or disable the cache
     *
     * @var bool
     */
    protected $cacheEnabled = true;

    public function __construct(ImageService $service)
    {
        $this->service = $service;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  int   $offset           Page offset
     * @param  int   $itemCountPerPage Number of items per page
     * @return array Page items
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($this->cacheEnabled()) {
            $page = $this->cache->getItem($this->getCacheId($offset, $itemCountPerPage));
            if ($page) {
                return $page;
            }
        }

        $page = $this->service->findPage($offset, $itemCountPerPage);

        if ($this->cacheEnabled()) {
            $this->cache->setItem($this->getCacheId($offset, $itemCountPerPage), $page);
        }

        return $page;
    }

    /**
     * Returns total number of images in project
     *
     * @return int Total number of images in project
     */
    public function count()
    {
        return $this->service->countImages();
    }

    /**
     * Sets a cache object
     *
     * @param CacheStorage $cache
     */
    public function setCache(CacheStorage $cache)
    {
        $this->cache = $cache;
    }

    public function getCacheId($offset, $itemCountPerPage)
    {
        return self::CACHE_KEY_PREFIX . md5($offset . $itemCountPerPage);
    }

    /**
     * Tells if there is an active cache object
     * and if the cache has not been disabled
     *
     * @return bool
     */
    public function cacheEnabled()
    {
        return (($this->cache !== null) && $this->cacheEnabled);
    }

    /**
     * Enables/Disables the cache
     *
     * @param  bool      $enable
     * @return DbAdapter
     */
    public function setCacheEnabled($enable)
    {
        $this->cacheEnabled = (bool) $enable;

        return $this;
    }
}
