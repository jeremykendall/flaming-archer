<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Middleware;

use \Slim\Middleware;
use \Fa\Dao\UserDao;

/**
 * Setup middleware
 *
 * Checks to see if application is ready to go. If not, issues appropriate
 * warnings and provides appropriate instructions.
 */
class Setup extends Middleware
{
    /**
     * User Dao
     *
     * @var Fa\Dao\UserDao User Dao
     */
    protected $userDao;

    /**
     * Public constructor
     *
     * @param \Fa\Dao\UserDao $userDao User DAO
     */
    public function __construct(UserDao $userDao)
    {
        $this->userDao = $userDao;
    }

    /**
     * Redirects user to /setup if no user exists in the database
     */
    public function call()
    {
        $app = $this->app;
        $userDao = $this->userDao;

        $app->hook('slim.before.router', function () use ($app, $userDao) {
            $users = $userDao->findAll();
            if (count($users) == 0) {
                $app->redirect('/setup');
            }
        });

        $this->next->call();
    }
}
