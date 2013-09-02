<?php

namespace FA\DI;

use FA\Authentication\Adapter\DbAdapter;
use FA\Authentication\Storage\EncryptedCookie;
use FA\Dao\ImageDao;
use FA\Dao\UserDao;
use FA\Middleware\Authentication;
use FA\Middleware\Navigation;
use FA\Middleware\Profile;
use FA\Pagination;
use FA\Service\FlickrService;
use FA\Service\FlickrServiceCache;
use FA\Service\ImageService;
use FA\Service\UserService;
use Pimple;
use Slim\Middleware\SessionCookie;
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\StorageFactory;

class Container extends Pimple
{
    public function __construct(Slim $app, array $config)
    {
        parent::__construct();

        $c = $this;

        $this['config'] = $config;
        $this['slim'] = $app;

        $this['db'] = $this->share(function () use ($c) {
            try {
                $db = new \PDO(
                    $c['config']['pdo']['dsn'],
                    $c['config']['pdo']['username'],
                    $c['config']['pdo']['password'],
                    $c['config']['pdo']['options']
                );

                return $db;
            } catch (\PDOException $e) {
                error_log('Database connection error in ' . $e->getFile() . ' on line ' . $e->getLine() . ': ' . $e->getMessage());
                die('Database connection error. Please check php error log.');
            }
        });

        $this['userDao'] = $this->share(function () use ($c) {
            return new UserDao($c['db']);
        });

        $this['authAdapter'] = $this->share(function () use ($c) {
            return new DbAdapter($c['userDao']);
        });

        $this['cache'] = $this->share(function () use ($c) {
            return StorageFactory::factory($c['config']['cache']);
        });

        $this['flickrService'] = function () use ($c) {
            return new FlickrService($c['config']['flickr.api.key']);
        };

        $this['flickrServiceCache'] = function () use ($c) {
            return new FlickrServiceCache($c['flickrService'], $c['cache']);
        };

        $this['imageService'] = function () use ($c) {
            return new ImageService(new ImageDao($c['db']), $c['flickrServiceCache']);
        };

        $this['auth'] = function () use ($c) {
            $auth = new AuthenticationService();
            $auth->setStorage($c['encryptedCookie']);
            $auth->setAdapter($c['authAdapter']);

            return $auth;
        };

        $this['encryptedCookie'] = function () use ($c) {
            return new EncryptedCookie($c['slim']);
        };

        $this['profileMiddleware'] = function () use ($c) {
            return new Profile($c['config']);
        };

        $this['navigationMiddleware'] = function () use ($c) {
            return new Navigation($c['auth']);
        };

        $this['authenticationMiddleware'] = function () use ($c) {
            return new Authentication($c['auth'], $c['config']);
        };

        $this['sessionCookieMiddleware'] = function () use ($c) {
            return new SessionCookie($c['config']['session_cookies']);
        };

        $this['userService'] = function () use ($c) {
            return new UserService($c['userDao'], $c['auth']);
        };

        $this['twig'] = function () {
            return new Twig();
        };

        $this['twigExtensionDebug'] = function () {
            return new \Twig_Extension_Debug();
        };

        $this['slimTwigExtension'] = function () {
            return new TwigExtension();
        };
        
        $this['pagination'] = function () {
            return new Pagination();
        };
    }
}
