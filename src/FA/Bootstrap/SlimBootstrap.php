<?php

namespace FA\Bootstrap;

use FA\DI\Container;
use Guzzle\Log\MessageFormatter;
use Guzzle\Log\MonologLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Monolog\Handler\ChromePHPHandler;
use Psr\Log\LogLevel;
use Slim\Log;
use Slim\Slim;
use Slim\Views\Twig;

class SlimBootstrap
{
    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Public constructor
     *
     * @param Slim Slim application instance
     * @param Container DI Container
     */
    public function __construct(Slim $app, Container $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    public function bootstrap()
    {
        $app = $this->app;
        $container = $this->container;

        $this->configureDevelopmentMode($app, $container);
        $this->configureLogging($app, $container);
        $this->configureView($app, $container);
        $this->addDefaultHeaders($app);
        $this->configureCustomErrorHandling($app);
        $this->addHooks($app, $container);
        $this->addMiddleware($app, $container);

        return $app;
    }

    public function configureDevelopmentMode(Slim $app, Container $container)
    {
        $config = $container['config'];

        $app->configureMode('development', function() use ($app, $container, $config) {
            $app->config('debug', false);

            $config['logger.app.level'] = LogLevel::DEBUG;
            $config['logger.guzzle.level'] = LogLevel::DEBUG;
            $config['twig']['environment']['auto_reload'] = true;
            $config['twig']['environment']['debug'] = true;

            $container['logger.app']->pushHandler(new ChromePHPHandler($config['logger.app.level']));
            $container['logger.guzzle']->pushHandler(new ChromePHPHandler($config['logger.guzzle.level']));

            $container['config'] = $config;
        });
    }

    public function configureLogging(Slim $app, Container $container)
    {
        $app->container->singleton('log', function () use ($container) {
            return $container['logger.app'];
        });

        $adapter = new MonologLogAdapter($container['logger.guzzle']);
        $logPlugin = new LogPlugin($adapter, MessageFormatter::DEBUG_FORMAT);
        $container['guzzleFlickrClient']->addSubscriber($logPlugin);
    }

    public function configureView(Slim $app, Container $container)
    {
        $app->view($container['twig']);
        $app->view->parserOptions = $container['config']['twig']['environment'];
        $app->view->parserExtensions = array($container['slimTwigExtension'], $container['twigExtensionDebug']);
        $app->view->getInstance()->getExtension('core')->setTimezone($container['config']['profile']['timezone']);
    }

    public function addHooks(Slim $app, Container $container)
    {
        $app->hook('slim.before.router', function () use ($app, $container) {
            $users = count($container['userDao']->findAll());
            $pathInfo = $app->request->getPathInfo();

            if ($users < 1 && $pathInfo != '/setup') {
                return $app->redirect('/setup');
            }
        });
    }

    // TODO: Make sure other exceptions get handled!
    public function configureCustomErrorHandling(Slim $app)
    {
        $app->error(function(\FA\Service\FlickrServiceUnavailableException $e) use ($app) {
            $app->render('flickr-down.html');
        });
    }

    public function addDefaultHeaders(Slim $app)
    {
        $app->response->headers->set('Content-Type', 'text/html; charset=utf-8');
    }

    public function addMiddleware(Slim $app, Container $container)
    {
        $app->add($container['profileMiddleware']);
        if ($container['googleAnalyticsMiddleware']) {
            $app->add($container['googleAnalyticsMiddleware']);
        }
        $app->add($container['navigationMiddleware']);
        $app->add($container['authenticationMiddleware']);
        $app->add($container['sessionCookieMiddleware']);
    }
}
