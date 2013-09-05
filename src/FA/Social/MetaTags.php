<?php

namespace FA\Social;

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
     * @param array $image Image data
     * @param array $profile Profile data
     */
    public function __construct(Request $request, array $image, array $profile)
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
        $day = $this->image['day'];
        $image = array_pop($this->image['sizes']['size']);

        $tags = array(
            'og:url' => $url . $path,
            'og:title' => sprintf('%s | Day %s', $this->profile['site_name'], $day),
            'og:description' => $this->profile['tagline'],
            'og:image' => $image['source'],
        );

        return $tags;
    }
}
