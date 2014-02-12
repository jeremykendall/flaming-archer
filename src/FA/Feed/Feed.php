<?php

namespace FA\Feed;

use FA\Service\ImageService;
use \Twig_Environment as View;
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
     * @var string Feed URI
     */
    protected $feedUri;

    /**
     * @var string Pubsubhubbub hub url
     */
    protected $hubUrl;

    /**
     * Public constructor
     *
     * @param ImageService $service Image service
     * @param View         $view    Twig_Environment
     * @param array        $profile Profile information
     * @param string       $baseUrl Site's base url
     */
    public function __construct(ImageService $service, View $view, array $profile, $baseUrl, $feedUri, $hubUrl)
    {
        $this->service = $service;
        $this->view = $view;
        $this->profile = $profile;
        $this->baseUrl = $baseUrl;
        $this->feedUri = $feedUri;
        $this->hubUrl = $hubUrl;
    }

    public function get($format = 'rss')
    {
        $feed = new FeedWriter();
        $feed->setTitle($this->profile['site_name']);
        $feed->setLink($this->baseUrl);
        $feed->setFeedLink(sprintf('%s%s', $this->baseUrl, $this->feedUri), $format);
        $feed->setDescription($this->profile['tagline']);
        $feed->addAuthor(array(
            'name' => $this->profile['photographer'],
            'uri'  => $this->profile['external_url'],
        ));
        $feed->setDateModified(time());
        $feed->addHub($this->hubUrl);

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

            $content = $this->view->render($this->getTemplate(), array(
                'photo' => $photo,
                'profile' => $this->profile,
                'baseUrl' => $this->baseUrl,
            ));

            $entry->setContent($content);
            $entry->setDateModified($photo->getPosted()->getTimestamp());
            $entry->setDateCreated($photo->getPosted()->getTimestamp());

            $feed->addEntry($entry);
        }

        return $feed->export($format);
    }

    public function publish($format = 'rss', $outfile = 'feed.xml')
    {
        return file_put_contents(
            sprintf('%s/public/%s', APPLICATION_PATH, $outfile),
            $this->get($format)
        );
    }

    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    protected function getTemplate()
    {
        $template = <<<__TWIG__
<h2>
    <em>
        <a href="{{ baseUrl }}/day/{{ photo.day }}">
            {{ photo.posted|date("d F Y") }}, Day {{ photo.day }}
        </a>
    </em>
</h2>
<p>
    <a href="http://flickr.com/photos/{{ profile.flickr_username }}/{{ photo.photoId }}/lightbox" target="_blank" title="View on Flickr">
        <img class="img-responsive img-thumbnail" src="{{ photo.featureSize.source }}" />
    </a>
</p>
<p>
    <h3>{{ photo.title }}</h3>
    <p>{{ photo.description|raw }}</p>
</p>
__TWIG__;

        return $template;
    }
}
