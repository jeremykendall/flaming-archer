<?php

namespace Tsf\Middleware;

use \Zend\Authentication\AuthenticationService;

/**
 * --- Library
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * Navigation class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class Navigation extends \Slim\Middleware
{

    /**
     * @var \Zend\Authentication\AuthenticationService 
     */
    private $auth;

    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    public function call()
    {
        $app = $this->app;
        $auth = $this->auth;
        $req = $app->request();

        $home = array('caption' => 'Home', 'href' => '/');
        $admin = array('caption' => 'Admin', 'href' => '/admin');
        $login = array('caption' => 'Login', 'href' => '/login');
        $logout = array('caption' => 'Logout', 'href' => '/logout');
        
        if ($auth->hasIdentity()) {
            $navigation = array($home, $admin, $logout);
        } else {
            $navigation = array($home, $login);
        }

        $this->app->hook('slim.before.router', function () use ($app, $auth, $req, $navigation) {

                foreach ($navigation as &$link) {
                    if ($link['href'] == $req->getPath()) {
                        $link['class'] = 'active';
                    } else {
                        $link['class'] = '';
                    }
                }

                $app->view()->appendData(array('navigation' => $navigation));
            }
        );

        $this->next->call();
    }

}

