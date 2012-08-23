<?php

require '../vendor/autoload.php';
$config = require_once __DIR__ . '/../config.php';

use Zend\Authentication\AuthenticationService;

try {
    $db = new PDO($config['pdo']['dsn'], $config['pdo']['username'], $config['pdo']['password'], $config['pdo']['options']);
} catch (PDOException $e) {
    die($e->getMessage());
}

$flickrService = new Tsf\Service\FlickrService($config['flickr.api.key']);
$flickrAPC = new Tsf\Service\FlickrServiceApc($flickrService);
$service = new Tsf\Service\ImageService(new Tsf\Dao\ImageDao($db), $flickrAPC);

// Prepare app
$app = new Slim($config['slim']);

$auth = new AuthenticationService();
$storage = new Tsf\Authentication\Storage\EncryptedCookie();
$auth->setStorage($storage);

$app->add(new Slim_Middleware_SessionCookie($config['cookies']));
$app->add(new Tsf\Middleware\Authentication($auth));
$app->add(new Tsf\Middleware\Navigation($auth));

// Prepare view
$twigView = new View_Twig();
$twigView->twigOptions = $config['twig'];
$app->view($twigView);

// Define routes
$app->get('/', function () use ($app, $service) {
        $images = $service->findAll();
        $app->render('index.html', array('images' => $images));
    }
);

$app->get('/:day', function($day) use ($app, $service) {
        $image = $service->find($day);
        
        if (!$image) {
            $app->notFound();
        }

        $app->render('images.html', $image);
    }
)->conditions(array('day' => '([1-9]\d?|[12]\d\d|3[0-5]\d|36[0-6])'));

$app->post('/admin/clear-cache', function() use ($app) {

        $log = $app->getLog();
        $cleared = null;
        $clear = $app->request()->post('clear');

        if ($clear == 1) {
            if (apc_clear_cache('user')) {
                $cleared = 'Cache was successfully cleared!';
            } else {
                $cleared = 'Cache was not cleared!';
                $log->error('Cache not cleared');
            }
        }

        $app->flash('cleared', $cleared);
        $app->redirect('/admin');
    }
);

$app->get('/admin', function() use ($app, $service) {
        $images = $service->findAll();
        $app->render('admin/index.html', array('images' => $images));
    }
);

$app->post('/admin/add-photo', function() use ($app, $service) {
        $data = $app->request()->post();
        $service->save($data);
        $app->redirect('/admin');
    }
);

$app->post('/admin/delete-photo', function() use ($app, $service) {
        $day = $app->request()->post('day');
        $service->delete($day);
        $app->redirect('/admin');
    }
);

$app->map('/login', function() use ($app, $db, $auth) {
        if ($app->request()->isGet()) {
            $app->render('login.html');
        }

        if ($app->request()->isPost()) {

            $post = $app->request()->post();

            $authAdapter = new Tsf\Authentication\Adapter\Db($db, $post['email'], $post['password']);
            $auth->setAdapter($authAdapter);
            $result = $auth->authenticate();

            if (!$result->isValid()) {
                $messages = $result->getMessages();
                $app->flash('error', $messages[0]);
                $app->redirect('/login');
            } else {
                $app->redirect('/');
            }
        }
    }
)->via('GET', 'POST');

$app->get('/logout', function() use ($app, $auth) {
        $auth->clearIdentity();
        $app->redirect('/');
    }
);

// Run app
$app->run();

function d($expression)
{
    var_dump($expression);
}

function dd($expression)
{
    d($expression);
    die();
}