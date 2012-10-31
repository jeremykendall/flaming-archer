<?php

namespace Tsf\Middleware;

/**
 * --- Library
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * Authentication class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class Authentication extends \Slim\Middleware
{

    /**
     * @var \Zend\Authentication\AuthenticationService 
     */
    private $auth;

    public function __construct(\Zend\Authentication\AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    public function call()
    {
        
        $app = $this->app;
        $auth = $this->auth;
        $req = $app->request();
        $config = array(
            'login.url' => '/login',
            'security.urls' => array(
                array('path' => '/admin'),
                array('path' => '/admin/.+')
            )
        );

        $this->app->hook('slim.before.router', function () use ($app, $auth, $req, $config) {
                $secured_urls = isset($config['security.urls']) ? $config['security.urls'] : array();
                foreach ($secured_urls as $surl) {
                    $patternAsRegex = $surl['path'];
                    if (substr($surl['path'], -1) === '/') {
                        $patternAsRegex = $patternAsRegex . '?';
                    }
                    $patternAsRegex = '@^' . $patternAsRegex . '$@';
                    if (preg_match($patternAsRegex, $req->getPathInfo())) {
                        if (!$auth->hasIdentity()) {
                            if ($req->getPath() !== $config['login.url']) {
                                $app->redirect($config['login.url']);
                            }
                        }
                    }
                }
            }
        );

        $this->next->call();
    }

}
