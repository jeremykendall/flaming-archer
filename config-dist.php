<?php

// SQLite database file
$sqlite = __DIR__ . '/db/flaming-archer.db';

// Slim configuration
$slim = array(
    'templates.path' => __DIR__ . '/templates',
    'log.level' => Slim\Log::ERROR,
    'log.enabled' => true,
    'log.writer' => new Slim\Extras\Log\DateTimeFileWriter(
        array(
            'path' => __DIR__ . '/logs',
            'name_format' => 'Y-m-d'
        )
    ),
    // Global, not SessionCookie, cookie settings
    'cookies.encrypt' => false, // must be false until https://github.com/codeguy/Slim/pull/606 is merged
    'cookies.secret_key' => 'CHANGE_ME',
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,
);

$config = array(
    'slim' => $slim,
    'profile' => array(
        'brand' => 'Flaming Archer',
        'site_name' => 'SITE NAME',
        'flickr_username' => 'YOUR FLICKR USERNAME',
        'photographer' => 'YOUR NAME',
        'tagline' => "TAGLINE",
        'external_url' => 'BLOG, FLICKR PROFILE, WHATEVER',
    ),
    'flickr.api.key' => 'YOUR FLICKR API KEY',
    'twig' => array(
        'environment' => array(
            'charset' => 'utf-8',
            'cache' => realpath(__DIR__ . '/templates/cache'),
            'auto_reload' => true,
            'strict_variables' => true,
            'autoescape' => true,
        ),
    ),
    'session_cookies' => array(
        'expires' => '20 minutes',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => false,
        'name' => 'slim_session',
        'secret' => $slim['cookies.secret_key'],
        'cipher' => $slim['cookies.cipher'],
        'cipher_mode' => $slim['cookies.cipher_mode'],
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
            'name' => 'filesystem',
            'options' => array(
                'ttl' => 60 * 60 * 24, // One day
                'namespace' => 'flaming-archer',
                'cache_dir' => realpath('../tmp')
            )
        ),
        'plugins' => array(
            'ExceptionHandler' => array(
                'throw_exceptions' => false
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

return $config;
