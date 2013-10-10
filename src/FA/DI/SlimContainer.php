<?php

namespace FA\DI;

use Guzzle\Log\MessageFormatter;
use Guzzle\Log\MonologLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Monolog\Handler\ChromePHPHandler;
use Psr\Log\LogLevel;
use Slim\Log;
use Slim\Slim;
use Slim\Views\Twig;

class SlimContainer extends Container
{
    public function __construct(Slim $app, array $config)
    {
        parent::__construct($config);
        $this['slim'] = $app;

        $this->configureApp();
    }

    protected function configureApp()
    {
        $app = $this['slim'];
        $c = $this;
        $config = $this['config'];

        // Set default headers
        $app->response->headers->set('Content-Type', 'text/html; charset=utf-8');

        $app->error(function(\FA\Service\FlickrServiceUnavailableException $e) use ($app) {
            $app->render('flickr-down.html');
        });

        $app->configureMode('development', function() use ($app, $c, &$config) {
            $app->config('debug', false);
            $config['logger.app.level'] = LogLevel::DEBUG;
            $config['logger.guzzle.level'] = LogLevel::DEBUG;
            $config['twig']['environment']['auto_reload'] = true;
            $config['twig']['environment']['debug'] = true;

            $c['logger.app']->pushHandler(new ChromePHPHandler($config['logger.app.level']));
            $c['logger.guzzle']->pushHandler(new ChromePHPHandler($config['logger.guzzle.level']));
        });

        $app->container->singleton('log', function () use ($c) {
            return $c['logger.app'];
        });

        $adapter = new MonologLogAdapter($c['logger.guzzle']);
        $logPlugin = new LogPlugin($adapter, MessageFormatter::DEBUG_FORMAT);
        $c['guzzleFlickrClient']->addSubscriber($logPlugin);

        // Add Middleware
        $app->add($c['profileMiddleware']);
        if ($c['googleAnalyticsMiddleware']) {
            $app->add($c['googleAnalyticsMiddleware']);
        }
        $app->add($c['navigationMiddleware']);
        $app->add($c['authenticationMiddleware']);
        $app->add($c['sessionCookieMiddleware']);

        // Prepare view
        $app->view($c['twig']);
        $app->view->parserOptions = $config['twig']['environment'];
        $app->view->parserExtensions = array($c['slimTwigExtension'], $c['twigExtensionDebug']);
        $app->view->getInstance()->getExtension('core')->setTimezone($c['config']['profile']['timezone']);
    }
}
