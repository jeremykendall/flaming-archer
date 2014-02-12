<?php

namespace FA\Event;

use FA\Model\Photo\Photo;
use Symfony\Component\EventDispatcher\Event;

class PhotoEvent extends Event
{
    /**
     * @var Photo
     */
    protected $photo;

    public function __construct(Photo $photo)
    {
        $this->setPhoto($photo);
    }

    /**
     * Get photo
     *
     * @return Photo photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param Photo $photo
     */
    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;
    }
}
