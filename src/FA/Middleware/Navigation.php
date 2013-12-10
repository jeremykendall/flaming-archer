<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Middleware;

use Slim\Middleware;
use Zend\Authentication\AuthenticationService;

/**
 * Navigation Middleware
 *
 * Constructs array of navigation items and appends them to the view. Navigation
 * items differ if user is authenticated or not.
 */
class Navigation extends Middleware
{

    /**
     * Authentication service
     *
     * @var Zend\Authentication\AuthenticationService
     */
    private $auth;

    /**
     * Public constructor
     *
     * @param Zend\Authentication\AuthenticationService $auth Authentication service
     */
    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Constructs array of navigation items and appends them to the view. Navigation
     * items differ if user is authenticated or not.
     */
    public function call()
    {
        $home = array('caption' => 'Home', 'href' => '/');
        $today = array('caption' => 'Today', 'href' => '/admin');
        $photos = array('caption' => 'Photos', 'href' => '/admin/photos');
        $settings = array('caption' => 'Settings', 'href' => '/admin/settings');
        $feed = array('caption' => 'Feed', 'href' => '/feed');
        $logout = array('caption' => 'Logout', 'href' => '/logout');

        if ($this->auth->hasIdentity()) {
            $navigation = array($home, $feed, $today, $photos, $settings, $logout);
        } else {
            $navigation = array($home, $feed);
        }

        foreach ($navigation as &$link) {
            if ($link['href'] == $this->app->request()->getPath()) {
                $link['class'] = 'active';
            } else {
                $link['class'] = '';
            }
        }

        $this->app->view()->appendData(array('navigation' => $navigation));

        $this->next->call();
    }
}
