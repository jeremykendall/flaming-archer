<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Service;

/**
 * Flickr service
 *
 * Abstracts calls to the Flickr API
 */
class FlickrService implements FlickrInterface
{
    /**
     * Flickr API key
     *
     * @var string
     */
    private $key;

    /**
     * Public constructor
     *
     * @param string $key Flickr API key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Finds photo on Flickr
     *
     * @param  int   $photoId Flickr photo id
     * @return array Photo data from Flickr
     */
    public function find($photoId)
    {
        $sizes = $this->getSizes($photoId);
        $info = $this->getInfo($photoId);

        return array_merge($sizes, $info);
    }

    /**
     * Returns sizes array for photo identified by Flickr photo id
     *
     * @param  int   $photoId
     * @return array Array of photo size information
     */
    public function getSizes($photoId)
    {
        $options = array(
            'method' => 'flickr.photos.getSizes',
            'api_key' => $this->key,
            'photo_id' => $photoId,
            'format' => 'json',
            'nojsoncallback' => 1
        );

        return $this->makeRequest($options);
    }

    /**
     * Returns info array for photo identified by Flickr photo id
     *
     * @param  int   $photoId
     * @return array Array of photo information
     */
    public function getInfo($photoId)
    {
        $options = array(
            'method' => 'flickr.photos.getInfo',
            'api_key' => $this->key,
            'photo_id' => $photoId,
            'format' => 'json',
            'nojsoncallback' => 1
        );

        return $this->makeRequest($options);
    }

    /**
     * Makes request to flickr API
     *
     * @param  array $options Query options
     * @return array Query result
     */
    protected function makeRequest(array $options)
    {
        $url = 'http://api.flickr.com/services/rest/?' . http_build_query($options);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
