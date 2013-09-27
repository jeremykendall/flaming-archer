<?php

namespace FA\Feed;

use FA\Service\ImageService;
use Slim\View;
use Zend\Feed\Writer\Feed as FeedWriter;

class Feed
{
    /**
     * @var string Site's base url
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $profile;

    /**
     * @var ImageService
     */
    protected $service;

    /**
     * @var View
     */
    protected $view;

    /**
     * Public constructor
     *
     * @param ImageService $service Image service
     * @param View $view Slim view
     * @param array $profile Profile information
     * @param string $baseUrl Site's base url
     */
    public function __construct(ImageService $service, View $view, array $profile, $baseUrl)
    {
        $this->service = $service;
        $this->view = $view;
        $this->profile = $profile;
        $this->baseUrl = $baseUrl;
    }

    public function get($format)
    {
        $feed = new FeedWriter();
        $feed->setTitle($this->profile['site_name']);
        $feed->setLink($this->baseUrl);
        $feed->setFeedLink($this->baseUrl . '/feed', $format);
        $feed->setDescription($this->profile['tagline']);
        $feed->addAuthor(array(
            'name' => $this->profile['photographer'],
            'uri'  => $this->profile['external_url'],
        ));
        $feed->setDateModified(time());
        $feed->addHub('http://pubsubhubbub.appspot.com/');

        $photos = $this->service->findAll();

        foreach ($photos as $photo) {
            $entry = $feed->createEntry();
            $entry->setTitle(sprintf('%s | Day %d', $photo->getTitle(), $photo->getDay()));
            $entry->setLink(sprintf('%s/day/%s', $this->baseUrl, $photo->getDay()));
            $entry->addAuthor(array(
                'name' => $this->profile['photographer'],
                'uri'  => $this->profile['external_url'],
            ));
            $entry->setDescription(sprintf('%s | Day %d', $photo->getTitle(), $photo->getDay()));

            $this->view->appendData(array('photo' => $photo, 'profile' => $this->profile));
            $content = $this->view->render('feed-content.html');

            $entry->setContent($content);
            $entry->setDateModified($photo->getPosted()->getTimestamp());
            $entry->setDateCreated($photo->getPosted()->getTimestamp());

            $feed->addEntry($entry);
        }

        return $feed->export($format);
    }

    public function setView($view)
    {
        $this->view = $view;
    }
}
