<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('America/Chicago');

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

$loader = require APPLICATION_PATH . '/vendor/autoload.php';
$loader->add('FA\\Tests\\', APPLICATION_PATH . '/tests');

define('SLIM_MODE', 'testing');
