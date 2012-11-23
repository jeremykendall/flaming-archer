<?php

// SQLite database file
$sqlite = __DIR__ . '/db/flaming-archer.db';

$config = array(
    'slim' => array(
        'templates.path' => __DIR__ . '/templates',
        'log.level' => Slim\Log::DEBUG,
        'log.enabled' => true,
        'log.writer' => new Slim\Extras\Log\DateTimeFileWriter(
            array(
                'path' => __DIR__ . '/logs',
                'name_format' => 'y-m-d'
            )
        )
    ),
    'twig' => array(
        'charset' => 'utf-8',
        'cache' => realpath(__DIR__ . '/templates/cache'),
        'auto_reload' => true,
        'strict_variables' => false,
        'autoescape' => true
    ),
    'cookies' => array(
        'expires' => '20 minutes',
        'path' => '/',
        'domain' => null,
        'secure' => true,
        'httponly' => false,
        'name' => 'slim_session',
        'secret' => 'CHANGE THIS SECRET',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC
    ),
    'flickr.api.key' => 'API KEY',
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
        )
    )
);

return $config;
