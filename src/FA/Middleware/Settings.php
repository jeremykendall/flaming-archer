<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Middleware;

use FA\DI\Container;
use Slim\Middleware;
use Zend\Authentication\AuthenticationService;

/**
 * Settings middleware
 *
 * Grabs environment specific settings and adds them to the container. These
 * are settings that are unknowable before the app is created and/or some
 * environment has been spun up.
 */
class Settings extends Middleware
{
    /**
     * @var Container
     */
    private $container;

    /**
        * Public constructor
        *
        * @param Container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Grabs application specific info and sets it on the container
     */
    public function call()
    {
        $this->container['baseUrl'] = $this->app->request->getUrl();
        $this->container['feedUri'] = $this->app->urlFor('feed');

        $this->next->call();
    }
}
