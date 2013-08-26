<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Middleware;

use Slim\Middleware;

/**
 * Site Profile Middleware
 *
 * Collects profile info from configuration to include in site templates
 */
class Profile extends Middleware
{
    /**
     * Config array
     *
     * @var array
     */
    private $config;

    /**
     * Public constructor
     *
     * @param array $config Configuration array
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Constructs array of profile items and appends them to the view.
     */
    public function call()
    {
        if (array_key_exists('profile', $this->config)) {
            $this->app->view()->appendData(array('profile' => $this->config['profile']));
        }

        $this->next->call();
    }
}
