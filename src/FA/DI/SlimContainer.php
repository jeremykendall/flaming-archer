<?php

namespace FA\DI;

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

        $app->configureMode('development', function() use ($app, $config) {
            $app->config(array(
                'log.level' => Log::DEBUG,
            ));

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
        $app->view->parserOptions = $this['config']['twig'];
        $app->view->parserExtensions = array($c['slimTwigExtension'], $c['twigExtensionDebug']);
    }
}
