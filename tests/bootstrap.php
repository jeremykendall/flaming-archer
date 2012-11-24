<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('America/Chicago');

require_once realpath(__DIR__ . '/../vendor/autoload.php');
require_once 'PHPUnit/Autoload.php';
require_once 'CommonDbTestCase.php';
require_once 'library/Fa/Composer/Script/ComposerScriptTestCase.php';

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

function d($expression) {
    var_dump($expression);
}

function dd($expression) {
    d($expression);
    die();
}