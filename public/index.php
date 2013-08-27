<?php

    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
require '../vendor/autoload.php';

$config = require_once __DIR__ . '/../config.php';

use FA\Authentication\Adapter\DbAdapter;
use FA\Authentication\Storage\EncryptedCookie;
use FA\Dao\ImageDao;
use FA\Dao\UserDao;
use FA\Middleware\Authentication;
use FA\Middleware\Navigation;
use FA\Middleware\Profile;
use FA\Pagination;
use FA\Service\FlickrService;
use FA\Service\FlickrServiceCache;
use FA\Service\ImageService;
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
    die('Database connection error. Please check php error log.');
}

$pagination = new Pagination();

$userDao = new UserDao($db);
$authAdapter = new DbAdapter($userDao, new Phpass\Hash());

$cache = StorageFactory::factory($config['cache']);
$flickrService = new FlickrService($config['flickr.api.key']);
$flickrServiceCache = new FlickrServiceCache($flickrService, $cache);

$service = new ImageService(new ImageDao($db), $flickrServiceCache);

// Prepare app
$app = new Slim($config['slim']);

// Dev mode settings
$app->configureMode('development', function() {
    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
});

$auth = new AuthenticationService();
$storage = new EncryptedCookie($app);
$auth->setStorage($storage);

// Add Middleware
$app->add(new Profile($config));
$app->add(new Navigation($auth));
$app->add(new Authentication($auth, $config));
$app->add(new SessionCookie($config['cookies']));

// Prepare view
$app->view(new Twig());
$app->view->parserOptions = $config['twig'];
$app->view->parserExtensions = array(new TwigExtension());

// Define routes
$app->get('/', function ($page = 1) use ($app, $service, $pagination) {
    $images = $service->findAll();
    $paginator = $pagination->newPaginator($images, $page, 10);

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $paginator->getPages(), 'home' => true));
});

$app->get('/page/:page', function ($page = 1) use ($app, $service, $pagination) {
    $images = $service->findAll();
    $paginator = $pagination->newPaginator($images, $page, 10);

    $home = ($page == 1) ? true : false;

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $paginator->getPages(), 'home' => $home));
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
            $app->flash('cacheSuccess', 'Cache cleared.');
        } else {
            $app->flash('cacheFailure', 'Problem clearing cache!');
            $log->error('Cache not cleared');
        }
    }

    $app->redirect('/admin/settings');
});

$app->get('/admin(/page/:page)', function ($page = 1) use ($app, $service, $pagination) {
    $images = $service->findAll();
    $paginator = $pagination->newPaginator($images, $page, 25);
    $projectDay = $service->getProjectDay();
    $daysLeft = 365 - $projectDay;
    $photoCount = $service->countImages();
    $percentage = ($photoCount / $projectDay) * 100;

    $viewData = array(
        'images' => $images, 
        'paginator' => $paginator, 
        'pages' => $paginator->getPages(),
        'projectDay' => $projectDay,
        'photoCount' => $photoCount,
        'percentage' => $percentage,
        'daysLeft' => $daysLeft,
    );

    $app->render('admin/index.html', $viewData);
});

$app->get('/admin/settings', function () use ($app) {
    $user = json_decode($app->getCookie('identity'), true);
    $app->render('admin/settings.html', array('user' => $user));
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

    $email = null;

    if ($app->request()->isPost()) {

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        $authAdapter->setCredentials($email, $_POST['password']);
        $auth->setAdapter($authAdapter);
        $result = $auth->authenticate();

        if (!$result->isValid()) {
            $messages = $result->getMessages();
            $app->flashNow('error', $messages[0]);
        } else {
            $app->redirect('/admin');
        }
    }

    $app->render('login.html', array('email' => $email));
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
