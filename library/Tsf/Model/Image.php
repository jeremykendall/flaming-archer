<?php

namespace Tsf\Model;

/**
 * Tsf Library
 * 
 * @category Tsf
 * @package Model
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */

/**
 * Image model
 * 
 * @category Tsf
 * @package Model
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class Image
{
    /**
     * Year in which you're running your 365 project.  String so you can create
     * a year range like 2012-2013, since you might not have started on Jan 1.
     * 
     * @var string 
     */
    protected $year;
    
    /**
     * Day image is assigned to 
     * 
     * @var int
     */
    protected $day;

    /**
     * Location of image for use in img tag
     * 
     * @var string 
     */
    protected $img;

    /**
     * Location of image for use in "View larger image" href tag
     * 
     * @var string 
     */
    protected $href;

    /**
     * Public constructor
     * 
     * @param string $img
     * @param string $href 
     */
    public function __construct($year, $day, $img, $href)
    {
        $this->year = $year;
        $this->day = $day;
        $this->img = $img;
        $this->href = $href;
    }
    
    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $value 
     */
    public function setYear($value)
    {
        $this->year = $value;
    }
    
    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int $value 
     */
    public function setDay($value)
    {
        $this->day = $value;
    }

    /**
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param string $value 
     */
    public function setImg($value)
    {
        $this->img = $value;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $value 
     */
    public function setHref($value)
    {
        $this->href = $value;
    }

}
