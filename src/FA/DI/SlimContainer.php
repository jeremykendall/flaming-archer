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

        // Add Middleware
        $app->add($c['profileMiddleware']);
        $app->add($c['navigationMiddleware']);
        $app->add($c['authenticationMiddleware']);
        $app->add($c['sessionCookieMiddleware']);
        $app->add($c['googleAnalyticsMiddleware']);

        // Prepare view
        $app->view($c['twig']);
        $app->view->parserOptions = $this['config']['twig'];
        $app->view->parserExtensions = array($c['slimTwigExtension'], $c['twigExtensionDebug']);

        $config = $this['config'];

        // Dev mode settings
        $app->configureMode('development', function() use ($app, $config) {
            $app->config(array(
                'log.enabled' => true,
                'log.level' => Log::DEBUG,
            ));

            $config['twig']['debug'] = true;
        });
    }
}
