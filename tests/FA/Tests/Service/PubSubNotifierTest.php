<?php

namespace FA\Tests\Service;

use FA\Service\PubSubNotifier;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\Response;

class PubSubNotifierTest extends \PHPUnit_Framework_TestCase
{
    protected $client;
    protected $request;
    protected $logger;
    protected $hubUrl;
    protected $feedUrl;
    protected $notifier;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->getMockBuilder('Guzzle\Http\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Guzzle\Http\Message\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->feedUrl = 'http://example.com/feed';
        $this->hubUrl = 'http://pubsub.example.com';

        $this->notifier = new PubSubNotifier($this->client, $this->logger, $this->hubUrl);
    }

    public function testNotify()
    {
        $this->client->expects($this->once())
            ->method('post')
            ->with($this->hubUrl, array(), array(
                'hub.mode' => 'publish',
                'hub.url' => $this->feedUrl,
            ))
            ->will($this->returnValue($this->request));

        $response = new Response(200);

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $this->logger->expects($this->at(0))
            ->method('debug')
            ->with('Notification request send to pubsubhubbub');

        $this->logger->expects($this->at(1))
            ->method('debug')
            ->with($this->request);

        $this->logger->expects($this->at(2))
            ->method('debug')
            ->with($response);

        $this->notifier->notify($this->feedUrl);
    }

    public function testNotifyBadResponseFromHub()
    {
        $this->client->expects($this->once())
            ->method('post')
            ->with($this->hubUrl, array(), array(
                'hub.mode' => 'publish',
                'hub.url' => $this->feedUrl,
            ))
            ->will($this->returnValue($this->request));

        $response = new Response(500);
        $e = BadResponseException::factory($this->request, $response);

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->throwException($e));

        $this->logger->expects($this->once())
            ->method('error')
            ->with(sprintf('Bad response from pubsubhubbub: %s', $e->getMessage()));

        $this->notifier->notify($this->feedUrl);
    }
}
