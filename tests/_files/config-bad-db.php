<?php

// This config is used to cause the SQLiteTest to throw an exception
$sqlite = '/this/path/does/not/exist';

$config = array(
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
);

return $config;
