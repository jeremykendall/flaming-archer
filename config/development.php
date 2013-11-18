<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

return array(
    'googleAnalyticsTrackingId' => '',
    'googleAnalyticsDomain' => '',
    'logger.app.level' => \Monolog\Logger::DEBUG,
    'logger.guzzle.level' => \Monolog\Logger::DEBUG,
    'slim' => array(
        'debug' => true,
    ),
    'twig' => array(
        'environment' => array(
            'auto_reload' => true,
            'debug' => true,
        ),
    ),
    'cache' => array(
        'plugins' => array(
            'ExceptionHandler' => array(
                'throw_exceptions' => true
            ),
        ),
    ),
);
