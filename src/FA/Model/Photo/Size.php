<?php

namespace FA\Model\Photo;

class Size
{
    /**
     * @var string Label
     */
    private $label;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var string Photo url
     */
    private $source;

    /**
     * @var string Url for the size's Flickr page
     */
    private $url;

    /**
     * Public constructor
     *
     * @param array $data OPTIONAL Size data
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    /**
     * Get label
     *
     * @return label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param $label the value to set
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get width
     *
     * @return width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set width
     *
     * @param $width the value to set
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Get height
     *
     * @return height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set height
     *
     * @param $height the value to set
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Get source
     *
     * @return source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set source
     *
     * @param $source the value to set
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Get url
     *
     * @return url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param $url the value to set
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Sets properties from array
     *
     * @param array $data Size data
     */
    public function fromArray(array $data)
    {
        foreach($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }
}
