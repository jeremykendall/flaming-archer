<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Service;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;
use FA\Model\Photo\Size;
use FA\Service\FlickrException;
use Guzzle\Common\Exception\MultiTransferException;
use Guzzle\Http\Client;
use Psr\Log\LoggerInterface;

/**
 * Flickr service
 *
 * Abstracts calls to the Flickr API
 */
class FlickrService implements FlickrInterface
{
    /**
     * Guzzle Client
     * 
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * Public constructor
     *
     * @param Client $client Guzzle client
     * @param LoggerInterface $log Logger
     */
    public function __construct(Client $client, LoggerInterface $log)
    {
        $this->client = $client;
        $this->log = $log;
    }

    /**
     * Finds photo on Flickr
     *
     * @param  Photo $photo Photo
     * @return Photo Photo data from Flickr
     */
    public function find(Photo $photo)
    {
        $info = $this->getInfo($photo->getPhotoId());

        // TODO: Why am I resetting photoId?  Could cause problems
        $photo->setPhotoId($info['photo']['id']);
        $photo->setTitle($info['photo']['title']['_content']);
        $photo->setDescription($info['photo']['description']['_content']);
        $photo->setTags($info['photo']['tags']['tag']);

        $sizeData = $this->getSizes($photo->getPhotoId());

        $sizes = new ArrayCollection();

        foreach ($sizeData['sizes']['size'] as $data) {
            $size = new Size();
            $size->setLabel($data['label']);
            $size->setWidth($data['width']);
            $size->setHeight($data['height']);
            $size->setSource($data['source']);
            $size->setUrl($data['url']);
            $sizes->set($size->getLabel(), $size);
        }

        $photo->setSizes($sizes);

        return $photo;
    }

    /**
     * Finds multiple photos
     *
     * @param Photo[] Array of photos to find
     * @return array Array of photos with Flickr data
     */
    public function findPhotos(array $photos)
    {
        $requests = array();

        foreach ($photos as $photo) {
            $requests[] = $this->client->get(
                sprintf('?%s', http_build_query(array(
                    'method' => 'flickr.photos.getSizes',
                    'photo_id' => $photo->getPhotoId(),
                )))
            );
            $requests[] = $this->client->get(
                sprintf('?%s', http_build_query(array(
                    'method' => 'flickr.photos.getInfo',
                    'photo_id' => $photo->getPhotoId(),
                )))
            );
        }

        // TODO: Handle exceptions gracefully. Add tests for exceptions
        try {
            $responses = $this->client->send($requests);

            $photoData = array();
            $sizeData = array();

            foreach ($responses as $response) {
                $body = $this->parseResponse($response->json());

                if (array_key_exists('photo', $body)) {
                    $photoData[$body['photo']['id']] = $body;
                }

                if (array_key_exists('sizes', $body)) {
                    $sizeData[] = $body;
                }
            }

            foreach ($photos as $photo) {
                $info = $photoData[$photo->getPhotoId()];
                $photo->setTitle($info['photo']['title']['_content']);
                $photo->setDescription($info['photo']['description']['_content']);
                $photo->setTags($info['photo']['tags']['tag']);

                $sizes = array_filter($sizeData, function($size) use ($photo) {
                    $url = $size['sizes']['size'][0]['url'];
                    if (strpos($url, (string) $photo->getPhotoId()) !== false) {
                        return $size;
                    }
                });

                foreach ($sizes as $data) {
                    foreach($data['sizes']['size'] as $size) {
                        $photo->setSize($size['label'], new Size($size));
                    }
                }
            }

            return $photos;
        } catch (MultiTransferException $e) {
            foreach ($e as $exception) {
                $this->log->error(
                    sprintf('Guzzle exception: %s', $exception->getMessage())
                );
            }

            foreach ($e->getFailedRequests() as $request) {
                $this->log->error(
                    sprintf('Guzzle failed request: %s', $request)
                );
            }

            foreach ($e->getSuccessfulRequests() as $request) {
                $this->log->error(
                    sprintf('Guzzle successful request: %s', $request)
                );
            }
        } catch (FlickrServiceException $e) {
            $this->log->error($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->log->error(sprintf('Exception processing photos: %s', $e->getMessage()));
        }
    }

    public function parseResponse($body)
    {
        if ($body['stat'] == 'ok') {
            return $body;
        }

        throw new FlickrServiceException(sprintf('Flickr crapped out: %s', $body['message']), $body['code']);
    }

    /**
     * Returns sizes array for photo identified by Flickr photo id
     *
     * @param  int   $photoId Flickr photoId
     * @return array Size data
     */
    protected function getSizes($photoId)
    {
        $options = array(
            'method' => 'flickr.photos.getSizes',
            'photo_id' => $photoId,
        );

        return $this->makeRequest($options);
    }

    /**
     * Returns info array for photo identified by Flickr photo id
     *
     * @param  int   $photoId Flickr photo id
     * @return array Array of photo information
     */
    protected function getInfo($photoId)
    {
        $options = array(
            'method' => 'flickr.photos.getInfo',
            'photo_id' => $photoId,
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
        $request = $this->client->get(sprintf('?%s', http_build_query($options)));
        $response = $request->send();
        return $this->parseResponse($response->json());
    }

    /**
     * Get client
     *
     * @return Client Guzzle client
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Set client
     *
     * @param Client $client Guzzle client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
