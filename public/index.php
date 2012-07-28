<?php

require '../vendor/autoload.php';

// Prepare app
$app = new Slim(array(
        'templates.path' => '../templates',
        'log.level' => 4,
        'log.enabled' => true,
        'log.writer' => new Log_FileWriter(array(
            'path' => '../logs',
            'name_format' => 'y-m-d'
        ))
    ));

// Prepare view
$twigView = new View_Twig();
$twigView->twigOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view($twigView);

// Define routes
$app->get('/', function () use ($app) {
        $app->render('home.html', array('message' => 'Welcome to 365.jeremykendall.net'));
    }
);

$app->get('/:year/:day', function($year, $day) use ($app) {
        $app->render('images.html', array('year' => $year, 'day' => $day));
    }
)->conditions(array('day' => '([1-9]\d?|[12]\d\d|3[0-5]\d|36[0-5])'));

// Run app
$app->run();
