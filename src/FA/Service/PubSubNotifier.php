<?php

namespace FA\Service;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\BadResponseException;
use Psr\Log\LoggerInterface;

class PubSubNotifier
{
    protected $client;

    protected $logger;

    protected $hubUrl;

    /**
     * Public constructor
     *
     * @param Client $client Http client
     * @param LoggerInterface PSR Logger Interface
     * @param string $hubUrl Url of pubsubhubbub hub to notify
     */
    public function __construct(ClientInterface $client, LoggerInterface $logger, $hubUrl)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->hubUrl = $hubUrl;
    }

    /**
     * Notify PubSubHubbub Hub
     *
     * @param string $feedUrl URL of the feed that has been updated
     * @param string $mode    Should be publish to notify hub of feed update
     */
    public function notify($feedUrl, $mode = 'publish')
    {
        $request = $this->client->post($this->hubUrl, array(), array(
            'hub.mode' => $mode,
            'hub.url' => $feedUrl,
        ));

        try {
            $response = $request->send();
            $this->logger->debug('Notification request send to pubsubhubbub');
            $this->logger->debug($request);
            $this->logger->debug($response);
        } catch (BadResponseException $e) {
            $this->logger->error(
                sprintf('Bad response from pubsubhubbub: %s', $e->getMessage())
            );
        }
    }
}
