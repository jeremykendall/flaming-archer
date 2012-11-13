<?php

require '../vendor/autoload.php';

$config = require_once __DIR__ . '/../config.php';

use Slim\Middleware\SessionCookie;
use Slim\Extras\Views\Twig;
use Tsf\Service\FlickrService;
use Tsf\Service\FlickrServiceApc;
use Tsf\Service\ImageService;
use Tsf\Dao\ImageDao;
use Tsf\Authentication\Storage\EncryptedCookie;
use Tsf\Authentication\Adapter\DbAdapter;
use Tsf\Middleware\Authentication;
use Tsf\Middleware\Navigation;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\StorageFactory;
use Zend\Cache\PatternFactory;

try {
    $db = new PDO($config['pdo']['dsn'], $config['pdo']['username'], $config['pdo']['password'], $config['pdo']['options']);
} catch (PDOException $e) {
    die($e->getMessage());
}

$authAdapter = new DbAdapter($db, new Phpass\Hash());

$flickrService = new FlickrService($config['flickr.api.key']);
//$flickrAPC = new FlickrServiceApc($flickrService);
$imageService = new ImageService(new ImageDao($db), $flickrService);

$options = array(
    'ttl' => 60 * 60 * 24, // One day
    'namespace' => 'flaming-archer'
);

$cache = StorageFactory::factory(array(
        'adapter' => array(
            'name' => 'apc',
            'options' => $options
        ),
        'plugins' => array(
            'ExceptionHandler' => array(
                'throw_exceptions' => false
            )
        )
    ));

$service = PatternFactory::factory('object', array(
        'object' => $imageService,
        'storage' => $cache
    ));

// Prepare app
$app = new Slim\Slim($config['slim']);

$auth = new AuthenticationService();
$storage = new EncryptedCookie();
$auth->setStorage($storage);

$app->add(new SessionCookie($config['cookies']));
$app->add(new Authentication($auth));
$app->add(new Navigation($auth));

// Prepare view
Twig::$twigOptions = $config['twig'];
$app->view(new Twig());

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

$app->post('/admin/clear-cache', function() use ($app, $service, $cache) {

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
    }
);

$app->get('/admin', function() use ($app, $service) {
        $images = $service->findAll();
        $app->render('admin/index.html', array('images' => $images));
    }
);

$app->post('/admin/add-photo', function() use ($app, $service, $cache) {
        $data = $app->request()->post();
        $service->save($data);
        $cache->flush();
        $app->redirect('/admin');
    }
);

$app->post('/admin/delete-photo', function() use ($app, $service, $cache) {
        $day = $app->request()->post('day');
        $service->delete($day);
        $cache->flush();
        $app->redirect('/admin');
    }
);

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