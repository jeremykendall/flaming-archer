<?php

namespace FA\Tests\Service;

use FA\Service\FlickrServiceCache;

class FlickrServiceCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlickrServiceCache
     */
    protected $serviceCache;

    /**
     * @var FlickrService
     */
    protected $service;

    /**
     * @var StorageInterface
     */
    protected $cache;

    protected function setUp()
    {
        $this->service = $this->getMockBuilder('FA\Service\FlickrService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceCache = new FlickrServiceCache($this->service, $this->cache);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->service = null;
    }

    public function testFindCacheMiss()
    {
        $this->cache->expects($this->once())
            ->method('getItem')
            ->with(1234)
            ->will($this->returnValue(null));

        $this->service->expects($this->once())
            ->method('find')
            ->with(1234)
            ->will($this->returnValue(array('Image information')));

        $this->cache->expects($this->once())
            ->method('addItem')
            ->with(1234, array('Image information'))
            ->will($this->returnValue(true));

        $result = $this->serviceCache->find(1234);

        $this->assertEquals(array('Image information'), $result);
    }

    public function testFindCacheHit()
    {
        $this->cache->expects($this->once())
            ->method('getItem')
            ->with(1234)
            ->will($this->returnValue(array('Image information')));

        $this->service->expects($this->never())->method('find');

        $result = $this->serviceCache->find(1234);

        $this->assertEquals(array('Image information'), $result);
    }
}
