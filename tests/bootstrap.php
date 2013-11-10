<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('America/Chicago');

$loader = require realpath(__DIR__ . '/../vendor/autoload.php');
$loader->add('FA\\', __DIR__);

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));
define('APPLICATION_CONFIG_PATH', realpath(__DIR__ . '/../config'));

function d($expression) {
    var_dump($expression);
}

function dd($expression) {
    d($expression);
    die();
}
