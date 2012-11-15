<?php

return array(
    'slim' => array(
        'templates.path' => __DIR__ . '/templates',
        'log.level' => 4,
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
    'flickr.api.key' => 'FLICKR API KEY',
    'pdo' => array(
        'dsn' => 'YOUR DB VENDOR:host=localhost;dbname=YOUR DB NAME',
        'username' => 'USERNAME',
        'password' => 'PASSWORD',
        'options' => array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    )
);
