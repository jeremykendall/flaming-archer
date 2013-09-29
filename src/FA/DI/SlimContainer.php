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

        $app->configureMode('development', function() use ($app, $c, &$config) {
            $c['logger']->pushHandler(new ChromePHPHandler(LogLevel::DEBUG));

            $adapter = new MonologLogAdapter($c['logger']);
            $logPlugin = new LogPlugin($adapter, MessageFormatter::DEFAULT_FORMAT);
            $c['guzzleFlickrClient']->addSubscriber($logPlugin);

            $config['twig']['environment']['auto_reload'] = true;
            $config['twig']['environment']['debug'] = true;
        });

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
    }
}
