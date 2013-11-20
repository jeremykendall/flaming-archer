<?php

namespace FA\Tests\Listener;

use FA\Event\PhotoEvent;
use FA\Listener\PhotoListener;
use FA\Model\Photo\Photo;
use FA\Paginator\Adapter\DbAdapter as PaginatorAdapter;
use FA\Tests\CustomTestCase;
use Mockery as m;

class PhotoListenerTest extends CustomTestCase
{
    /**
     * @var PhotoListener
     */
    protected $listener;

    /**
     * @var Mock implementing both ClearByPrefixInterface and StorageInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = m::mock(
            'Zend\Cache\Storage\ClearByPrefixInterface, Zend\Cache\Storage\StorageInterface'
        );

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new PhotoListener($this->cache, $this->logger);
    }

    protected function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testClearByPrefixInterfaceRequired()
    {
        $this->setExpectedException(
            '\Exception',
            'Cache must implement both Zend\Cache\Storage\ClearByPrefixInterface ' .
            'and Zend\Cache\Storage\StorageInterface interfaces.'
        );

        $cache = $this->getMockBuilder('Zend\Cache\Storage\ClearByPrefixInterface')
            ->disableOriginalConstructor()
            ->getMock();

        new PhotoListener($cache, $this->logger);
    }

    public function testStorageInterfaceRequired()
    {
        $this->setExpectedException(
            '\Exception',
            'Cache must implement both Zend\Cache\Storage\ClearByPrefixInterface ' .
            'and Zend\Cache\Storage\StorageInterface interfaces.'
        );

        $cache = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')
            ->disableOriginalConstructor()
            ->getMock();

        new PhotoListener($cache, $this->logger);
    }

    public function testOnPhotoSave()
    {
        $photo = new Photo();
        $photo->setId('1234');

        $event = new PhotoEvent($photo);
        $event->setName('photo.save');

        $this->logger->expects($this->once())
            ->method('info')
            ->with(sprintf(
                'Calling %s for %s',
                'FA\Listener\PhotoListener::onPhotoSave',
                $event->getName()
            ));

        $this->cache->shouldReceive('clearByPrefix')
            ->with(PaginatorAdapter::CACHE_KEY_PREFIX)
            ->once();

        $this->listener->onPhotoSave($event);
    }

    public function testOnPhotoDelete()
    {
        $photo = new Photo();
        $photo->setPhotoId('1234');

        $event = new PhotoEvent($photo);
        $event->setName('photo.delete');

        $this->logger->expects($this->once())
            ->method('info')
            ->with(sprintf(
                'Calling %s for %s',
                'FA\Listener\PhotoListener::onPhotoDelete',
                $event->getName()
            ));

        $this->cache->shouldReceive('removeItem')
            ->with('1234')
            ->once();

        $this->cache->shouldReceive('clearByPrefix')
            ->with(PaginatorAdapter::CACHE_KEY_PREFIX)
            ->once();

        $this->listener->onPhotoDelete($event);
    }
}
