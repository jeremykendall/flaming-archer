<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Middleware;

use Fa\Dao\UserDao;
use Slim\Middleware;

/**
 * Setup middleware
 *
 * Checks to ensure the application is configured
 */
class Setup extends Middleware
{
    /**
     * @var UserDao
     */
    private $dao;

    /**
     * Public constructor
     *
     * @param UserDao $dao UserDao
     */
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    public function call()
    {
        $app = $this->app;
        $dao = $this->dao;

        $checkSetup = function () use ($app, $dao) {
            if ($this->dao->userExists() === false) {
                return $this->app->redirect('/setup');
            }
        };
        
        $this->app->hook('slim.before.router', $checkSetup, 2);
            
        $this->next->call();
    }
}
