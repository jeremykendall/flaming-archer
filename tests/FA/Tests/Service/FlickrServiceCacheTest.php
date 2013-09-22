<?php

namespace FA\Tests\Service;

use FA\Model\Photo\Photo;
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

    /**
     * @var Photo
     */
    protected $photo;

    protected function setUp()
    {
        $this->photo = new Photo();
        $this->photo->setId(1234);

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
            ->with($this->photo->getId())
            ->will($this->returnValue(null));

        $this->service->expects($this->once())
            ->method('find')
            ->with($this->photo)
            ->will($this->returnValue($this->photo));

        $this->cache->expects($this->once())
            ->method('addItem')
            ->with($this->photo->getId(), $this->photo)
            ->will($this->returnValue(true));

        $result = $this->serviceCache->find($this->photo);

        $this->assertEquals($this->photo, $result);
    }

    public function testFindCacheHit()
    {
        $this->cache->expects($this->once())
            ->method('getItem')
            ->with($this->photo->getId())
            ->will($this->returnValue($this->photo));

        $this->service->expects($this->never())->method('find');

        $result = $this->serviceCache->find($this->photo);

        $this->assertEquals($this->photo, $result);
    }
}
