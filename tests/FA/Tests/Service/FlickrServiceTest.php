<?php

namespace FA\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;
use FA\Service\FlickrService;

class FlickrServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlickrService
     */
    protected $service;

    /**
     * @var array
     */
    protected static $config;

    /**
     * @var ArrayCollection
     */
    protected $sizes;

    /**
     * @var Photo
     */
    protected $photo;

    /**
     * @var Client Guzzle client
     */
    protected $client;

    /**
     * @var Log
     */
    protected $log;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$config = include APPLICATION_CONFIG_PATH . '/config.php';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = $this->getMockBuilder('Guzzle\Http\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->log = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new FlickrService($this->client, $this->log);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->service = null;
    }

    public function testParseResponseOk()
    {
        $body = array(
            'stat' => 'ok',
        );

        $this->assertEquals($body, $this->service->parseResponse($body));
    }

    public function testParseResponseFlickrUnavailable()
    {
        $body = array(
            'stat' => 'fail',
            'code' => FlickrService::SERVICE_UNAVAILABLE,
            'message' => 'Sad panda',
        );

        $this->setExpectedException(
            'FA\Service\FlickrServiceUnavailableException',
            sprintf('FLICKR IS DOWN: %s', $body['message']),
            FlickrService::SERVICE_UNAVAILABLE
        );

        $this->service->parseResponse($body);
    }

    public function testParseResponseFail()
    {
        $body = array(
            'stat' => 'fail',
            'code' => 999,
            'message' => 'Something happened',
        );

        $this->setExpectedException(
            'FA\Service\FlickrServiceException',
            sprintf('Flickr crapped out: %s', $body['message']),
            $body['code']
        );

        $this->service->parseResponse($body);
    }
}
