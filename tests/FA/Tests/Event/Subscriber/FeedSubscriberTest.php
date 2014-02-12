<?php

namespace FA\Tests\Event\Subscriber;

use FA\Event\FeedEvent;
use FA\Event\Subscriber\FeedSubscriber;
use FA\Tests\ConfigTestCase;

/**
 * @group events
 */
class FeedSubscriberTest extends ConfigTestCase
{
    /**
     * @var Feed $feed
     */
    protected $feed;

    /**
     * @var FeedSubscriber
     */
    protected $subscriber;

    /**
     * @var PubSubNotifier
     */
    protected $notifier;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->feed = $this->getMockBuilder('FA\Feed\Feed')
            ->disableOriginalConstructor()
            ->getMock();

        $this->notifier = $this->getMockBuilder('FA\Service\PubSubNotifier')
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->subscriber = new FeedSubscriber($this->feed, $this->notifier, $this->logger);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetSubscribedEvents()
    {
        $events = FeedSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey('content.change', $events);
        $this->assertArrayHasKey('feed.publish', $events);
    }

    public function testOnContentChange()
    {
        $format = 'rss';
        $outfile = 'feed.xml';
        $feedUrl = 'http://example.com/feed';
        $notifyMode = 'publish';

        $event = new FeedEvent(
            $format, $outfile, $feedUrl, $notifyMode
        );
        $event->setName('content.change');

        $this->logger->expects($this->once())
            ->method('info')
            ->with(sprintf(
                'Calling %s for %s',
                'FA\Event\Subscriber\FeedSubscriber::onContentChange',
                $event->getName()
            ));

        $this->feed->expects($this->once())
            ->method('publish')
            ->with($format, $outfile);

        $this->subscriber->onContentChange($event);
    }

    public function testOnFeedPublish()
    {
        $format = 'rss';
        $outfile = 'feed.xml';
        $feedUrl = 'http://example.com/feed';
        $notifyMode = 'publish';

        $event = new FeedEvent(
            $format, $outfile, $feedUrl, $notifyMode
        );
        $event->setName('feed.publish');

        $this->logger->expects($this->once())
            ->method('info')
            ->with(sprintf(
                'Calling %s for %s',
                'FA\Event\Subscriber\FeedSubscriber::onFeedPublish',
                $event->getName()
            ));

        $this->notifier->expects($this->once())
            ->method('notify')
            ->with($feedUrl, $notifyMode);

        $this->subscriber->onFeedPublish($event);
    }
}
