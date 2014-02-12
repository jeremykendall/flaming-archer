<?php

namespace FA\DI;

use FA\Acl;
use FA\Authentication\Adapter\DbAdapter;
use FA\Dao\ImageDao;
use FA\Dao\UserDao;
use FA\Event\FeedEvent;
use FA\Event\Subscriber\FeedSubscriber;
use FA\Event\Subscriber\PhotoSubscriber;
use FA\Feed\Feed;
use FA\Middleware\GoogleAnalytics;
use FA\Middleware\Navigation;
use FA\Middleware\Profile;
use FA\Middleware\Settings;
use FA\Pagination;
use FA\Paginator\Adapter\DbAdapter as PaginatorAdapter;
use FA\Service\FlickrService;
use FA\Service\ImageService;
use FA\Service\PubSubNotifier;
use FA\Service\UserService;
use FA\Social\MetaTags;
use Guzzle\Cache\Zf2CacheAdapter as ZendCacheAdapter;
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\CallbackCanCacheStrategy;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use JeremyKendall\Slim\Auth\Middleware\Authorization;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple;
use Slim\Middleware\SessionCookie;
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use \Twig_Environment;
use \Twig_Extension_Debug;
use \Twig_Loader_String;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\StorageFactory;
use Zend\Paginator\Paginator;

class Container extends Pimple
{
    public function __construct(array $config)
    {
        parent::__construct();
        $this['config'] = $config;

        $this->configureContainer();
    }

    protected function configureContainer()
    {
        $c = $this;

        $c['baseUrl'] = null;
        $c['feedUri'] = null;

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

        $this['logger.app'] = $this->share(function () use ($c) {
            $log = new Logger('app');
            $log->pushHandler(
                new StreamHandler(
                    $c['config']['logger.app.logfile'],
                    $c['config']['logger.app.level']
                )
            );

            return $log;
        });

        $this['logger.guzzle'] = $this->share(function () use ($c) {
            $log = new Logger('guzzle');
            $log->pushHandler(
                new StreamHandler(
                    $c['config']['logger.guzzle.logfile'],
                    $c['config']['logger.guzzle.level']
                )
            );

            return $log;
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
            return new FlickrService($c['guzzleFlickrClient'], $c['logger.app']);
        };

        $this['flickrServiceCache'] = function () use ($c) {
            return new FlickrService($c['guzzleFlickrCachingClient'], $c['logger.app']);
        };

        $this['imageService'] = function () use ($c) {
            return new ImageService(new ImageDao($c['db']), $c['flickrServiceCache']);
        };

        $this['feed.event'] = function () use ($c) {
            return new FeedEvent(
                $c['config']['feed.format'],
                $c['config']['feed.outfile'],
                sprintf('%s%s', $c['baseUrl'], $c['feedUri'])
            );
        };

        $this['feed.writer'] = function () use ($c) {
            return new Feed(
                $c['imageService'],
                $c['twig.loader.string'],
                $c['config']['profile'],
                $c['baseUrl'],
                $c['feedUri'],
                $c['config']['pubsubhubbub.url']
            );
        };

        $this['paginatorAdapter'] = function () use ($c) {
            $adapter = new PaginatorAdapter($c['imageService']);
            $adapter->setCache($c['cache']);

            return $adapter;
        };

        $this['zendPaginator'] = function () use ($c) {
            return new Paginator($c['paginatorAdapter']);
        };

        $this['acl'] = function () {
            return new Acl();
        };

        $this['auth'] = function () use ($c) {
            $auth = new AuthenticationService();
            $auth->setAdapter($c['authAdapter']);

            return $auth;
        };

        $this['slimAuthMiddleware'] = function () use ($c) {
            return new Authorization($c['auth'], $c['acl']);
        };

        $this['profileMiddleware'] = function () use ($c) {
            return new Profile($c['config']);
        };

        $this['navigationMiddleware'] = function () use ($c) {
            return new Navigation($c['auth']);
        };

        $this['sessionCookieMiddleware'] = function () use ($c) {
            return new SessionCookie($c['config']['session_cookies']);
        };

        $this['googleAnalyticsMiddleware'] = function () use ($c) {
            if ($c['config']['googleAnalyticsTrackingId'] && $c['config']['googleAnalyticsDomain']) {
                return new GoogleAnalytics(
                    $c['auth'],
                    $c['config']['googleAnalyticsTrackingId'],
                    $c['config']['googleAnalyticsDomain']
                );
            }
        };

        $this['settingsMiddleware'] = function () use ($c) {
            return new Settings($c);
        };

        $this['userService'] = function () use ($c) {
            return new UserService($c['userDao'], $c['auth']);
        };

        $this['twig.loader.string'] = function () {
            $loader = new Twig_Loader_String();
            $twig = new Twig_Environment($loader);

            return $twig;
        };

        $this['slim.twig'] = function () {
            return new Twig();
        };

        $this['twig.extension.debug'] = function () {
            return new Twig_Extension_Debug();
        };

        $this['slim.twig.extension'] = function () {
            return new TwigExtension();
        };

        $this['pagination'] = function () use ($c) {
            return new Pagination($c['paginatorAdapter']);
        };

        $this['metaTags'] = function () use ($c) {
            return new MetaTags(
                $c['request'],
                $c['image'],
                $c['config']['profile']
            );
        };

        $this['guzzleFlickrClient'] = $this->share(function () use ($c) {
            $client = new Client($c['config']['flickr.api.endpoint']);
            $client->setDefaultOption('query', array(
                'api_key' => $c['config']['flickr.api.key'],
                'format' => 'json',
                'nojsoncallback' => 1,
            ));

            return $client;
        });

        $this['guzzleFlickrCachingClient'] = $this->share(function () use ($c) {
            $canCache = new CallbackCanCacheStrategy(
                function ($request) {
                    if ($request->getQuery()->get('method') === 'flickr.photos.search') {
                        $params = $request->getParams();
                        $params['cache.disallowed-by-callback'] = true;

                        return false;
                    }

                    return true;
                }
            );

            $storage = new DefaultCacheStorage(new ZendCacheAdapter($c['cache']));

            $cachePlugin = new CachePlugin(array(
                'can_cache' => $canCache,
                'storage' => $storage,
            ));

            $client = $c['guzzleFlickrClient'];
            $client->addSubscriber($cachePlugin);

            $client->getEventDispatcher()->addListener('request.before_send', function (Event $event) use ($c) {
                $request = $event['request'];

                $cacheResult = array(
                    'flickr.method' => $request->getQuery()->get('method'),
                    'params' => $request->getParams(),
                );

                $c['logger.guzzle']->debug('CACHE RESULT', $cacheResult);
            },
            -999);

            return $client;
        });

        $this['defaultFlickrSearchOptions'] = function () use ($c) {
            $tz = new \DateTimeZone($c['config']['profile']['timezone']);
            $date = new \DateTime('today', $tz);
            $minUpload = $date->format('Y-m-d H:i:sP');

            $date->modify('+23 hours 59 minutes');
            $maxUpload = $date->format('Y-m-d H:i:sP');

            $options = array(
                'user_id' => $c['config']['flickr.user.id'],
                'min_upload_date' => $minUpload,
                'max_upload_date' => $maxUpload,
                'extras' => 'url_sq, url_t, url_q',
            );

            return $options;
        };

        $this['dispatcher'] = function () use ($c) {
            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber($c['event_subscriber.photo']);
            $dispatcher->addSubscriber($c['event_subscriber.feed']);

            return $dispatcher;
        };

        $this['event_subscriber.photo'] = function () use ($c) {
            return new PhotoSubscriber(
                $c['cache'],
                $c['logger.app']
            );
        };

        $this['event_subscriber.feed'] = function () use ($c) {
            return new FeedSubscriber(
                $c['feed.writer'],
                $c['pubSubNotifier'],
                $c['logger.app']
            );
        };

        $this['pubSubNotifier'] = function () use ($c) {
            return new PubSubNotifier(
                new Client(),
                $c['logger.app'],
                $c['config']['pubsubhubbub.url']
            );
        };

        $this['now'] = function () use ($c) {
            $tz = new \DateTimeZone($c['config']['profile']['timezone']);

            return new \DateTime('now', $tz);
        };
    }
}
