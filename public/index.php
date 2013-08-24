<?php

require '../vendor/autoload.php';

$config = require_once __DIR__ . '/../config.php';

use Fa\Authentication\Adapter\DbAdapter;
use Fa\Authentication\Storage\EncryptedCookie;
use Fa\Dao\ImageDao;
use Fa\Dao\UserDao;
use Fa\Middleware\Authentication;
use Fa\Middleware\Navigation;
use Fa\Middleware\Profile;
use Fa\Service\FlickrService;
use Fa\Service\FlickrServiceCache;
use Fa\Service\ImageService;
use Slim\Middleware\SessionCookie;
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\StorageFactory;

try {
    $db = new PDO(
        $config['pdo']['dsn'],
        $config['pdo']['username'],
        $config['pdo']['password'],
        $config['pdo']['options']
    );
} catch (PDOException $e) {
    error_log('Database connection error in ' . $e->getFile() . ' on line ' . $e->getLine() . ': ' . $e->getMessage());
    die('Database connection error. Please check error logs.');
}

$userDao = new UserDao($db);
$authAdapter = new DbAdapter($userDao, new Phpass\Hash());

$cache = StorageFactory::factory($config['cache']);
$flickrService = new FlickrService($config['flickr.api.key']);
$flickrServiceCache = new FlickrServiceCache($flickrService, $cache);

$service = new ImageService(new ImageDao($db), $flickrServiceCache);

// Prepare app
$app = new Slim($config['slim']);

$app->configureMode('development', function() {
    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
});

$auth = new AuthenticationService();
$storage = new EncryptedCookie($app);
$auth->setStorage($storage);

$app->add(new Profile($config));
$app->add(new Navigation($auth));
$app->add(new Authentication($auth, $config));
$app->add(new SessionCookie($config['cookies']));

// Prepare view
$app->view(new Twig());
$app->view->parserOptions = $config['twig'];
$app->view->parserExtensions = array(new TwigExtension());

// Define routes
$app->get('/', function () use ($app, $service) {
    $images = $service->findAll();
    $app->render('index.html', array('images' => $images));
});

$app->get('/day/:day', function($day) use ($app, $service) {
    $image = $service->find($day);

    if (!$image) {
        $app->notFound();
    }

    $app->render('day.html', $image);
})->conditions(array('day' => '([1-9]\d?|[12]\d\d|3[0-5]\d|36[0-6])'));

$app->post('/admin/clear-cache', function() use ($app, $cache) {
    $log = $app->getLog();
    $cleared = null;
    $clear = $app->request()->post('clear');

    if ($clear == 1) {
        if ($cache->flush()) {
            $cleared = 'Cache was successfully cleared!';
        } else {
            $cleared = 'Cache was not cleared!';
            $log->error('Cache not cleared');
        }
    }

    $app->flash('cleared', $cleared);
    $app->redirect('/admin');
});

$app->get('/admin', function() use ($app, $service) {
    $images = $service->findAll();
    $app->render('admin/index.html', array('images' => $images));
});

$app->post('/admin/add-photo', function() use ($app, $service, $cache) {
    $data = $app->request()->post();
    $service->save($data);
    $cache->flush();
    $app->redirect('/admin');
});

$app->post('/admin/delete-photo', function() use ($app, $service, $cache) {
    $day = $app->request()->post('day');
    $service->delete($day);
    $cache->flush();
    $app->redirect('/admin');
});

$app->map('/login', function() use ($app, $auth, $authAdapter) {
    if ($app->request()->isGet()) {
        $app->render('login.html');
    }

    if ($app->request()->isPost()) {

        $post = $app->request()->post();

        $authAdapter->setCredentials($post['email'], $post['password']);
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
})->via('GET', 'POST');

$app->get('/logout', function() use ($app, $auth) {
    $auth->clearIdentity();
    $app->redirect('/');
});

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
