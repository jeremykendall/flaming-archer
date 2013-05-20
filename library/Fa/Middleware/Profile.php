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
 * Collects details from configuration to include in site templates
 */
class Profile extends Middleware
{

    /**
     * Array
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
     * Constructs array of navigation items and appends them to the view. Navigation
     * items differ if user is authenticated or not.
     */
    public function call()
    {
        if (array_key_exists('profile', $this->config)) {
            $this->app->view()->appendData(array('profile' => $this->config['profile']));
        }

        $this->next->call();
    }
}
