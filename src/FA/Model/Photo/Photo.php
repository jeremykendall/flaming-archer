<?php

namespace FA\Model\Photo;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\BaseModel;
use FA\Model\Photo\Size;

class Photo extends BaseModel
{
    /**
     * @var int Flickr photo id
     */
    private $photoId;

    /**
     * @var array Photo owner data
     */
    private $owner;

    /**
     * @var int Project day
     */
    private $day;

    /**
     * @var \DateTime Date posted to project
     */
    private $posted;

    /**
     * @var string Title
     */
    private $title;

    /**
     * @var string Description
     */
    private $description;

    /**
     * @var array Tags
     */
    private $tags;

    /**
     * @var ArrayCollection Photo sizes
     */
    private $sizes;

    /**
     * Public constructor
     *
     * @param array $data OPTIONAL photo data
     */
    public function __construct(array $data = array())
    {
        $this->tags = array();
        $this->sizes = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get photoId
     *
     * @return int Photo id
     */
    public function getPhotoId()
    {
        return $this->photoId;
    }

    /**
     * Set photoId
     *
     * @param $photoId the value to set
     */
    public function setPhotoId($photoId)
    {
        $this->photoId = (int) $photoId;
    }

    /**
     * Get owner data
     *
     * @return array owner
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * Set owner
     *
     * @param array $owner Owner data
     */
    public function setOwner(array $owner = null)
    {
        $this->owner = $owner;
    }

    /**
     * Get day
     *
     * @return day
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set day
     *
     * @param $day the value to set
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * Get posted
     *
     * @return \DateTime Photo posted date
     */
    public function getPosted()
    {
        if (false === $this->posted instanceof \DateTime && $this->posted !== null) {
            $this->posted = \DateTime::createFromFormat('Y-m-d H:i:s', $this->posted);
        }

        return $this->posted;
    }

    /**
     * Set posted
     *
     * @param \DateTime $posted the value to set
     */
    public function setPosted(\DateTime $posted)
    {
        $this->posted = $posted;
    }

    /**
     * Get title
     *
     * @return title
     */
    public function getTitle()
    {
        if (!$this->title) {
            $this->title = 'Untitled';
        }

        return $this->title;
    }

    /**
     * Set title
     *
     * @param $title the value to set
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get description
     *
     * @return description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param $description the value to set
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get tags
     *
     * @return array tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param array $tags the value to set
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get sizes
     *
     * @return ArrayCollection sizes
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Set sizes
     *
     * @param ArrayCollection $sizes the value to set
     */
    public function setSizes(ArrayCollection $sizes)
    {
        $this->sizes = $sizes;
    }

    /**
     * Gets Size specified by $label
     *
     * @param string label Size label
     * @return Size Photo size
     */
    public function getSize($label)
    {
        return $this->sizes->get($label);
    }

    /**
     * Sets size on size collection
     *
     * @param string $label Size label
     * @param Size   $size  Photo size
     */
    public function setSize($label, Size $size)
    {
        $this->sizes->set($label, $size);
    }

    public function getFeatureSize()
    {
        return $this->getSize('Medium 640');
    }

    public function toArray()
    {
        return array(
            'id'          => $this->getId(),
            'photoId'     => $this->getPhotoId(),
            'owner'       => $this->getOwner(),
            'day'         => $this->getDay(),
            'posted'      => $this->getPosted(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'tags'        => $this->getTags(),
            'sizes' => $this->getSizes(),
        );
    }
}
