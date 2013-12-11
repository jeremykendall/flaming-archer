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
     * Get outfile
     *
     * @return outfile
     */
    public function getOutfile()
    {
        return $this->outfile;
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
     * Get notifyMode
     *
     * @return notifyMode
     */
    public function getNotifyMode()
    {
        return $this->notifyMode;
    }
}
