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
     * @var Photo Photo data
     */
    protected $photo;

    /**
     * @var array Profile data
     */
    protected $profile;

    /**
     * @var Size Photo size
     */
    protected $size;

    /**
     * @var string Description
     */
    protected $description;

    /**
     * Public constructor
     *
     * @param Request $request Slim Request
     * @param Photo   $photo   Photo
     * @param array   $profile Profile data
     */
    public function __construct(Request $request, Photo $photo, array $profile)
    {
        $this->request = $request;
        $this->photo = $photo;
        $this->profile = $profile;
        $sizes = $photo->getSizes()->toArray();
        $this->size = array_pop($sizes);

        if ($description = $photo->getDescription()) {
            $this->description = $description;
        } else {
            $this->description = $this->profile['tagline'];
        }
    }

    public function getTags()
    {
        return array_merge(
            $this->getOpenGraphTags(),
            $this->getTwitterPhotoCard()
        );
    }

    /**
     * Should return the largest photo size available
     *
     * @return array Array of tag names and values
     */
    public function getOpenGraphTags()
    {
        $tags = array(
            'og:url' => sprintf(
                '%s%s',
                $this->request->getUrl(),
                $this->request->getPath()
            ),
            'og:title' => sprintf(
                '%s | Day %s',
                $this->photo->getTitle(),
                $this->photo->getDay()
            ),
            'og:description' => $this->description,
            'og:image' => $this->size->getSource(),
        );

        return $tags;
    }

    public function getTwitterPhotoCard()
    {
        $day = $this->photo->getDay();

        $tags = array(
            'twitter:card' => 'photo',
            'twitter:site' => $this->profile['twitter_username'],
            'twitter:creator' => $this->profile['twitter_username'],
            'twitter:title' => sprintf(
                '%s | Day %s',
                $this->photo->getTitle(),
                $this->photo->getDay()
            ),
            'twitter:image:src' => $this->size->getSource(),
            'twitter:image:width' => $this->size->getWidth(),
            'twitter:image:height' => $this->size->getHeight(),
        );

        return $tags;
    }
}
