<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

/**
 * EDIT THESE KEYS TO COMPLETE APPLICATION CONFIGURATION
 *
 * Remember, you MUST have a Flickr API key in order to use Flaming Archer
 */
return array(
    'flickr.api.key' => '@@@ Your Flickr API key @@@',
    // Leave blank unless you'd like to use Google Analytics
    'googleAnalyticsTrackingId' => '',
    'googleAnalyticsDomain' => '',
    // Change these settings to whatever you like
    'profile' => array(
        'brand' => 'Flaming Archer',
        'site_name' => '365 Days of Photography',
        'flickr_username' => '@@@ Your Flickr username @@@',
        'photographer' => '@@@ Your name @@@',
        'tagline' => "@@@ Some clever tagline @@@",
        'external_url' => '@@@ Website, Flickr profile, blog, etc. @@@',
        'twitter_username' => '@YOUR_TWITTER_USERNAME',
        'timezone' => 'America/Chicago',
    ),
    // Change this to a random-ish string. It's used for encrypting cookies.
    'cookies.secret_key' => 'CHANGE_ME',
);
