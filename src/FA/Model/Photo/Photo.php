<?php

namespace FA\Model\Photo;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Size;

class Photo
{
    /**
     * @var int Photo id
     */
    private $id;

    /**
     * @var int Flickr photo id
     */
    private $photoId;

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
     */
    public function __construct()
    {
        $this->tags = array();
        $this->sizes = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param $id the value to set
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get photoId
     *
     * @return photoId
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
        $this->photoId = $photoId;
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
     * @return posted
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * Set posted
     *
     * @param $posted the value to set
     */
    public function setPosted($posted)
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
}
