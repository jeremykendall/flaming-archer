<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Hook;

use Fa\Dao\UserDao;
use Slim\Slim;

/**
 * Setup hook
 *
 * Checks to ensure the application is configured and redirects user to setup 
 * if not
 */
class SetupHook
{
    /**
     * @var Slim
     */
    private $app;

    /**
     * @var UserDao
     */
    private $dao;

    /**
     * Public constructor
     *
     * @param Slim $app Slim application
     * @param UserDao $dao User dao
     */
    public function __construct(Slim $app, UserDao $dao)
    {
        $this->app = $app;
        $this->dao = $dao;
    }

    /**
     * Checks to see if app has been configured. Does nothing if so, redirects 
     * to setup if not.
     */
    public function confirmSetup()
    {
        if ($this->dao->userExists() === false) {
            return $this->app->redirect('/setup');
        }
    }
}
