<?php

require '../vendor/autoload.php';

$config = require_once __DIR__ . '/../config.php';

try {
    $db = new PDO($config['pdo']['dsn'], $config['pdo']['username'], $config['pdo']['password'], $config['pdo']['options']);
} catch (PDOException $e) {
    die($e->getMessage());
}

$service = new Tsf\Service\Images(
        new Tsf\Dao\Images($db),
        new Tsf\Flickr($config['slim']['flickr.api.key'])
);

// Prepare app
$app = new Slim($config['slim']);

$app->add(new Slim_Middleware_SessionCookie(array(
        'expires' => '20 minutes',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => false,
        'name' => 'slim_session',
        'secret' => 'Super secret secret!',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC
        )
    )
);

// Prepare view
$twigView = new View_Twig();
$twigView->twigOptions = $config['twig'];
$app->view($twigView);

// Define routes
$app->get('/', function () use ($app, $service) {
        $log = $app->getLog();

        if (apc_fetch('images') === false) {
            $images = $service->findAll();
            apc_store('images', $images);
            $log->debug('images cache miss');
        } else {
            $images = apc_fetch('images');
            $log->debug('images cache hit');
        }

//        die(var_dump($images[0]['sizes']));

        $app->render('home.html', array('images' => $images));
    }
);

$app->get('/:day', function($day) use ($app, $service) {
        $log = $app->getLog();

        if (apc_fetch('day' . $day) === false) {
            $image = $service->find($day);

            if (!$image) {
                $app->notFound();
            }

            apc_store('day' . $day, $image);
            $log->debug("day$day cache miss");
        } else {
            $image = apc_fetch('day' . $day);
            $log->debug("day$day cache hit");
        }

        if (!$image) {
            $app->notFound();
        }

        $app->render('images.html', $image);
    }
)->conditions(array('day' => '([1-9]\d?|[12]\d\d|3[0-5]\d|36[0-6])'));

$app->map('/clear-cache', function() use ($app) {

        if ($app->request()->isGet()) {
            $app->render('clear-cache.html');
        }

        if ($app->request()->isPost()) {
            $log = $app->getLog();
            $cleared = 'Cache was not cleared';
            $clear = $app->request()->post('clear');
            if ($clear == 1) {
                if (apc_clear_cache('user')) {
                    $log->error('Cache cleared');
                    $cleared = 'Cache was successfully cleared';
                }
            }

            $log->error('Cache not cleared');
            $app->flash('cleared', $cleared);
            $app->redirect('/clear-cache');
        }
    }
)->via('GET', 'POST');

$app->get('/cache-clear', function() use ($app) {
        apc_delete('images');
        $app->redirect('/');
    }
);

// Run app
$app->run();