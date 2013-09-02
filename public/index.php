<?php

require '../vendor/autoload.php';

$config = require_once __DIR__ . '/../config.php';

use FA\DI\Container;
use Slim\Slim;

// Prepare app
$app = new Slim($config['slim']);
$container = new Container($app, $config);

$app->hook('slim.before.router', function () use ($app, $container) {
    $users = count($container['userDao']->findAll());
    $pathInfo = $app->request->getPathInfo();

    if ($users < 1 && $pathInfo != '/setup') {
        return $app->redirect('/setup');
    }
});

$app->get('/setup', function () use ($app, $container) {
    if (count($container['userDao']->findAll()) > 0) {
        $app->halt(403);
    }

    $app->render('setup.html', array('email' => $email));
});

$app->post('/setup', function () use ($app, $container) {
    if (count($container['userDao']->findAll()) > 0) {
        $app->halt(403, 'NO MOAR USERS ALLOWED');
    }

    $params = $app->request()->post();

    $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($email) {
        try {
            $user = $container['userService']->createUser($email, $params['password'], $params['confirm-password']);
            $app->log->info(sprintf('New user %s has been created', $user['email']));
            $app->flash('joinSuccess', sprintf('Congrats %s! Now log in and get started!', $user['email']));
            $app->redirect('/login');
        } catch (\PDOException $p) {
            $app->log->error(sprintf('Database error creating account for %s: %s', $email, $p->getMessage()));
            $app->flash('error', sprintf("Database error creating your account. Stop doing whatever bad thing you're doing!", $email));
        } catch (\Exception $e) {
            $app->log->error(sprintf('Error creating account for %s: %s', $email, $e->getMessage()));
            $app->flash('error', $e->getMessage());
        }
    } else {
        $app->flash('error', sprintf("'%s' is not a valid email address", $params['email']));
    }

    $app->redirect('/setup');
});

// Define routes
$app->get('/', function ($page = 1) use ($app, $container) {
    $images = $container['imageService']->findAll();
    $paginator = $container['pagination']->newPaginator($images, $page, 10);

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $paginator->getPages(), 'home' => true));
});

$app->get('/page/:page', function ($page = 1) use ($app, $container) {
    $images = $container['imageService']->findAll();
    $paginator = $container['pagination']->newPaginator($images, $page, 10);

    $home = ($page == 1) ? true : false;

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $paginator->getPages(), 'home' => $home));
});

$app->get('/day/:day', function($day) use ($app, $container) {
    $image = $container['imageService']->find($day);

    if (!$image) {
        $app->notFound();
    }

    $app->render('day.html', $image);
})->conditions(array('day' => '([1-9]\d?|[12]\d\d|3[0-5]\d|36[0-6])'));

$app->post('/admin/clear-cache', function() use ($app, $container) {
    $log = $app->getLog();
    $cleared = null;
    $clear = $app->request()->post('clear');

    if ($clear == 1) {
        if ($container['cache']->flush()) {
            $app->flash('cacheSuccess', 'Cache cleared.');
        } else {
            $app->flash('cacheFailure', 'Problem clearing cache!');
            $log->error('Cache not cleared');
        }
    }

    $app->redirect('/admin/settings');
});

$app->get('/admin(/page/:page)', function ($page = 1) use ($app, $container) {
    $images = $container['imageService']->findAll();
    $paginator = $container['pagination']->newPaginator($images, $page, 25);
    $projectDay = $container['imageService']->getProjectDay();
    $daysLeft = 365 - $projectDay;
    $photoCount = $container['imageService']->countImages();
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

$app->post('/admin/user', function () use ($app, $container) {
    $user = json_decode($app->getCookie('identity'), true);
    $params = $app->request()->post();

    $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && ($email != $user['email'])) {
        $container['userService']->updateEmail($user, $email);
        $app->log->info(sprintf('Email changed from %s to %s', $user['email'], $email));
        $app->flash('emailSuccess', 'Your email is now ' . $email);
    }

    if ($params['form-type'] == 'change-password' && $params['password']) {
        $app->log->info(sprintf('About to change password for %s', $user['email']));
        try {
            $result = $container['userService']->changePassword($user['email'], $params['password'], $params['new-password'], $params['confirm-password']);
            $app->log->info(sprintf('Password changed for %s', $user['email']));
            $app->flash('passwordSuccess', 'Password changed!');
        } catch (\Exception $e) {
            $app->log->error(sprintf('Error changing password: %s', $e->getMessage()));
            $app->flash('passwordError', $e->getMessage());
        }
    }

    $app->redirect('/admin/settings');
});

$app->post('/admin/add-photo', function() use ($app, $container) {
    $data = $app->request()->post();
    $container['imageService']->save($data);
    $container['cache']->flush();
    $app->redirect('/admin');
});

$app->post('/admin/delete-photo', function() use ($app, $container) {
    $day = $app->request()->post('day');
    $container['imageService']->delete($day);
    $container['cache']->flush();
    $app->redirect('/admin');
});

$app->map('/login', function() use ($app, $container) {

    $email = null;

    if ($app->request()->isPost()) {

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $result = $container['userService']->authenticate($email, $_POST['password']);

        if ($result->isValid()) {
            $app->redirect('/admin');
        } else {
            $messages = $result->getMessages();
            $app->flashNow('error', $messages[0]);
        }
    }

    $app->render('login.html', array('email' => $email));
})->via('GET', 'POST');

$app->get('/logout', function() use ($app, $container) {
    $container['userService']->clearIdentity();
    $app->redirect('/');
});

// Run app
$app->run();
