<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Middleware;

use Zend\Authentication\AuthenticationService;

/**
 * Authentication middleware
 *
 * Checks if user is authenticated when user visiting secured URI. Will redirect
 * a user to login if they attempt to visit a secured URI and are not authenticated
 */
class Authentication extends \Slim\Middleware
{

    /**
     * Authentication service
     *
     * @var AuthenticationService
     */
    private $auth;

    /**
     * Config array
     *
     * @var array
     */
    private $config;

    /**
     * Public constructor
     *
     * @param AuthenticationService $auth   Authentication service
     * @param array                 $config Configuration array
     */
    public function __construct(AuthenticationService $auth, array $config)
    {
        $this->auth = $auth;
        $this->config = $config;
    }

    /**
     * Uses 'slim.before.router' to check for authentication when visitor attempts
     * to access a secured URI. Will redirect unauthenticated user to login page.
     */
    public function call()
    {
        $app = $this->app;
        $req = $app->request();
        $auth = $this->auth;
        $config = $this->config;

        $checkAuth = function () use ($app, $auth, $req, $config) {
            $securedUrls = isset($config['secured.urls']) ? $config['secured.urls'] : array();
            foreach ($securedUrls as $url) {
                $urlPattern = '@^' . $url['path'] . '$@';
                if (preg_match($urlPattern, $req->getPathInfo()) === 1 && $auth->hasIdentity() === false) {
                    return $app->redirect($config['login.url']);
                }             
            }
        };
        
        $this->app->hook('slim.before.router', $checkAuth);

        $this->next->call();
    }

}
