<?php

require_once realpath(__DIR__ . '/../vendor/autoload.php');
require_once 'PHPUnit/Autoload.php';
require_once 'CommonDbTestCase.php';

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

function d($expression) {
    var_dump($expression);
}

function dd($expression) {
    d($expression);
    die();
}