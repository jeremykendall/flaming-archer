<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

// SQLite database file
$sqlite = __DIR__ . '/../db/flaming-archer.db';

return array(
    'flickr.api.endpoint' => 'http://api.flickr.com/services/rest',
    'logger.app.logfile' => __DIR__ . '/../logs/app.log',
    'logger.app.level' => \Monolog\Logger::ERROR,
    'logger.guzzle.logfile' => __DIR__ . '/../logs/guzzle.log',
    'logger.guzzle.level' => \Monolog\Logger::ERROR,
    'pagination' => array(
        'admin.itemCountPerPage' => 25,
        'public.itemCountPerPage' => 25,
    ),
    'slim' => array(
        'debug' => false,
        'templates.path' => __DIR__ . '/../templates',
        'cookies.encrypt' => true,
        'cookies.secret_key' => 'CHANGE_ME',
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    ),
    'twig' => array(
        'environment' => array(
            'charset' => 'utf-8',
            'cache' => realpath(__DIR__ . '/../templates/cache'),
            'auto_reload' => false,
            'strict_variables' => true,
            'autoescape' => true,
            'debug' => false,
        ),
    ),
    'session_cookies' => array(
        'expires' => '2 weeks',
    ),
    'database' => $sqlite,
    'pdo' => array(
        'dsn' => 'sqlite:' . $sqlite,
        'username' => null,
        'password' => null,
        'options' => array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    ),
    // All cache config happens here, so you can alter these settings to use
    // whichever of the Zend\Cache adapters and settings you like.
    // http://framework.zend.com/manual/2.0/en/index.html#zend-cache
    'cache' => array(
        'adapter' => array(
            'name' => 'apc',
            'options' => array(
                'ttl' => 60 * 60 * 24, // One day
                'namespace' => 'flaming-archer',
            )
        ),
        'plugins' => array(
            'ExceptionHandler' => array(
                'throw_exceptions' => true
            ),
            'Serializer'
        ),
    ),
    'login.url' => '/login',
    'secured.urls' => array(
        array('path' => '/admin'),
        array('path' => '/admin/.+')
    ),
);
