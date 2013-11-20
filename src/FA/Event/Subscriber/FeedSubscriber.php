<?php

namespace FA\Event\Subscriber;

use FA\Event\FeedEvent;
use FA\Feed\Feed;
use FA\Service\PubSubNotifier;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedSubscriber implements EventSubscriberInterface
{
    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @var PubSubNotifier
     */
    protected $pubSubNotifier;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Public constructor
     */
    public function __construct(
        Feed $feed,
        PubSubNotifier $pubSubNotifer,
        LoggerInterface $logger
    )
    {
        $this->feed = $feed;
        $this->pubSubNotifier = $pubSubNotifer;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'content.change' => array(
                array('onContentChange', 5),
                array('onFeedPublish', 0),
            ),
            'feed.publish' => array('onFeedPublish', 0),
        );
    }

    public function onContentChange(FeedEvent $event)
    {
        $this->logger->info(
            sprintf('Calling %s for %s', __METHOD__, $event->getName())
        );
        $this->feed->publish($event->getFormat(), $event->getOutfile());
    }

    public function onFeedPublish(FeedEvent $event)
    {
        $this->logger->info(
            sprintf('Calling %s for %s', __METHOD__, $event->getName())
        );
        $this->pubSubNotifier->notify($event->getFeedUrl(), $event->getNotifyMode());
    }
}
