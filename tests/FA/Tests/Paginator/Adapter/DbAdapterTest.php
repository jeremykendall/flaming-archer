<?php

namespace FA\Tests\Paginator\Adapter;

use FA\Paginator\Adapter\DbAdapter;

class DbAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $adapter;
    private $cache;
    private $service;

    protected function setUp()
    {
        $this->cache = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = $this->getMockBuilder('FA\Service\ImageService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = new DbAdapter($this->service);
    }

    public function testImplementsAdapterInterface()
    {
        $this->assertInstanceOf('Zend\Paginator\Adapter\AdapterInterface', $this->adapter);
    }

    public function testGetItemsCacheNotEnabled()
    {
        $offset = 50;
        $itemCountPerPage = 25;

        $this->cache->expects($this->never())
            ->method('getItem');

        $this->service->expects($this->once())
            ->method('findPage')
            ->with($offset, $itemCountPerPage);

        $this->adapter->getItems($offset, $itemCountPerPage);
    }

    public function testGetItemsCacheMiss()
    {
        $this->adapter->setCache($this->cache);

        $offset = 50;
        $itemCountPerPage = 25;
        $cacheId = $this->adapter->getCacheId($offset, $itemCountPerPage);

        $this->cache->expects($this->once())
            ->method('getItem')
            ->with($cacheId)
            ->will($this->returnValue(null));

        $this->service->expects($this->once())
            ->method('findPage')
            ->with($offset, $itemCountPerPage)
            ->will($this->returnValue(array('valid' => 'page data')));

        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($cacheId, array('valid' => 'page data'))
            ->will($this->returnValue(null));

        $this->adapter->getItems($offset, $itemCountPerPage);
    }

    public function testGetItemsCacheHit()
    {
        $this->adapter->setCache($this->cache);

        $offset = 50;
        $itemCountPerPage = 25;
        $cacheId = $this->adapter->getCacheId($offset, $itemCountPerPage);

        $this->cache->expects($this->once())
            ->method('getItem')
            ->with($cacheId)
            ->will($this->returnValue(array('valid' => 'page data')));

        $this->service->expects($this->never())
            ->method('findPage');

        $this->cache->expects($this->never())
            ->method('setItem');

        $this->adapter->getItems($offset, $itemCountPerPage);
    }

    public function testCacheEnabledTrueIfCacheEnabledTrueAndCacheNotNull()
    {
        $enabled = $this->adapter->setCacheEnabled(true);
        $this->assertInstanceOf('FA\Paginator\Adapter\DbAdapter', $enabled);
        $this->adapter->setCache($this->cache);
        $this->assertTrue($this->adapter->cacheEnabled());
    }

    public function testCacheEnabledFalseIfCacheEnabledTrueButCacheNull()
    {
        $this->adapter->setCacheEnabled(true);
        $this->assertFalse($this->adapter->cacheEnabled());
    }

    public function testCacheEnabledFalseIfCacheEnabledFalseButCacheNotNull()
    {
        $this->adapter->setCacheEnabled(false);
        $this->adapter->setCache($this->cache);
        $this->assertFalse($this->adapter->cacheEnabled());
    }

    public function testCount()
    {
        $this->service->expects($this->once())
            ->method('countImages');

        $this->adapter->count();
    }

    public function testGetCacheId()
    {
        $offset = 2;
        $itemCountPerPage = 25;

        $expected = DbAdapter::CACHE_KEY_PREFIX . md5($offset . $itemCountPerPage);
        $actual = $this->adapter->getCacheId($offset, $itemCountPerPage);

        $this->assertEquals($expected, $actual);
    }
}
