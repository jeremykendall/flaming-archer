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

        $directives = array(
            'default-src' => "'none'",
            'font-src' => "'self' netdna.bootstrapcdn.com",
            'img-src' => "'self' *.staticflickr.com www.google-analytics.com data:",
            'script-src' => "'self' 'unsafe-inline' cdnjs.cloudflare.com netdna.bootstrapcdn.com www.google-analytics.com",
            'style-src' => "'self' netdna.bootstrapcdn.com",
            'report-uri' => '/csp-report',
        );

        $policy = null;

        foreach ($directives as $name => $value) {
            $policy .= sprintf('%s %s;', $name, $value);
        }

        // Set default headers
        $app->response->headers->set('Content-Type', 'text/html; charset=utf-8');
        $app->response->headers->set('Content-Security-Policy', $policy);
        $app->response->headers->set('X-Webkit-CSP', $policy);

        $app->configureMode('development', function() use ($app, &$config) {
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
        $app->view->parserOptions = $config['twig']['environment'];
        $app->view->parserExtensions = array($c['slimTwigExtension'], $c['twigExtensionDebug']);
    }
}
