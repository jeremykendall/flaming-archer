<?php

namespace FA\Event;

use Symfony\Component\EventDispatcher\Event;

class FeedEvent extends Event
{
    protected $format;

    protected $outfile;

    protected $feedUrl;

    protected $notifyMode;

    public function __construct($format, $outfile, $feedUrl, $notifyMode = 'publish')
    {
        $this->format = $format;
        $this->outfile = $outfile;
        $this->feedUrl = $feedUrl;
        $this->notifyMode = $notifyMode;
    }

    /**
     * Get format
     *
     * @return format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set format
     *
     * @param $format the value to set
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * Get outfile
     *
     * @return outfile
     */
    public function getOutfile()
    {
        return $this->outfile;
    }

    /**
     * Set outfile
     *
     * @param $outfile the value to set
     */
    public function setOutfile($outfile)
    {
        $this->outfile = $outfile;
    }

    /**
     * Get feedUrl
     *
     * @return string feedUrl
     */
    public function getFeedUrl()
    {
        return $this->feedUrl;
    }

    /**
     * Set feedUrl
     *
     * @param string $feedUrl Feed url
     */
    public function setFeedUrl($feedUrl)
    {
        $this->feedUrl = $feedUrl;
    }

    /**
     * Get notifyMode
     *
     * @return notifyMode
     */
    public function getNotifyMode()
    {
        return $this->notifyMode;
    }

    /**
     * Set notifyMode
     *
     * @param $notifyMode the value to set
     */
    public function setNotifyMode($notifyMode)
    {
        $this->notifyMode = $notifyMode;
    }
}
