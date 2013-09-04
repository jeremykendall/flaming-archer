<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Middleware;

use Zend\Authentication\AuthenticationService;

/**
 * Google Analytics Middleware
 *
 * Adds tracking code unless user is logged in.
 */
class GoogleAnalytics extends \Slim\Middleware
{
    /**
     * Authentication service
     *
     * @var AuthenticationService
     */
    private $auth;

    /**
     * Domain where tracking ID is valid
     *
     * @var string Domain where tracking ID is valid
     */
    private $domain;

    /**
     * Google Analytics tracking ID
     *
     * @var string Google Analytics tracking ID
     */
    private $trackingId;

    /**
     * Public constructor
     *
     * @param AuthenticationService $auth       Authentication service
     * @param string                $trackingId Tracking ID
     * @param string                $domain     Domain where tracking ID is valid
     */
    public function __construct(AuthenticationService $auth, $trackingId, $domain)
    {
        $this->auth = $auth;
        $this->trackingId = $trackingId;
        $this->domain = $domain;
    }

    /**
     * Adds tracking code unless user is logged in.
     */
    public function call()
    {
        $pathInfo = $this->app->request->getPathInfo();

        if (false === $this->auth->hasIdentity() && $pathInfo != '/login') {
            $this->app->view()->appendData(array(
                'ga' => array(
                    'trackingId' => $this->trackingId,
                    'domain' => $this->domain,
                ),
            ));
        }

        $this->next->call();
    }
}
