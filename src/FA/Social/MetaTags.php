<?php

namespace FA\Social;

use FA\Model\Photo\Photo;
use Slim\Http\Request;

class MetaTags
{
    /**
     * @var Request Slim Request
     */
    protected $request;

    /**
     * @var array Image data
     */
    protected $image;

    /**
     * @var array Profile data
     */
    protected $profile;

    /**
     * Public constructor
     *
     * @param Request $request Slim Request
     * @param Photo   $image   Photo
     * @param array   $profile Profile data
     */
    public function __construct(Request $request, Photo $image, array $profile)
    {
        $this->request = $request;
        $this->image = $image;
        $this->profile = $profile;
    }

    /**
     * Should return the largest image size available
     *
     * @return array Array of tag names and values
     */
    public function getOpenGraphTags()
    {
        $url = $this->request->getUrl();
        $path = $this->request->getPath();
        $day = $this->image->getDay();
        $sizes = $this->image->getSizes()->toArray();
        $size = array_pop($sizes);

        $tags = array(
            'og:url' => $url . $path,
            'og:title' => sprintf('%s | Day %s', $this->profile['site_name'], $day),
            'og:description' => $this->profile['tagline'],
            'og:image' => $size->getSource(),
        );

        return $tags;
    }

    public function getTwitterPhotoCard()
    {
        $sizes = $this->image->getSizes()->toArray();
        $size = array_pop($sizes);

        $day = $this->image->getDay();

        $tags = array(
            'twitter:card' => 'photo',
            'twitter:site' => $this->profile['twitter_username'],
            'twitter:creator' => $this->profile['twitter_username'],
            'twitter:title' => sprintf('%s | Day %s', $this->profile['site_name'], $day),
            'twitter:image:src' => $size->getSource(),
            'twitter:image:width' => $size->getWidth(),
            'twitter:image:height' => $size->getHeight(),
        );

        return $tags;
    }
}
