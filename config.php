<?php

return array(
    'slim' => array(
        'flickr.api.key' => '74a85f6ba16bb97b66b455af78da0d0a',
        'templates.path' => __DIR__ . '/templates',
        'log.level' => 4,
        'log.enabled' => true,
        'log.writer' => new Log_FileWriter(
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
    'pdo' => array(
        'dsn' => 'mysql:host=localhost;dbname=365',
        'username' => 'testuser',
        'password' => 'testpass',
        'options' => array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    )
);