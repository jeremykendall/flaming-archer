<?php

require '../vendor/autoload.php';

$config = require_once __DIR__ . '/../config.php';

if (getenv('SLIM_MODE')) {
    $config['slim']['mode'] = getenv('SLIM_MODE');
}

use FA\DI\SlimContainer;
use FA\Model\Photo\Photo;
use Slim\Slim;

// Prepare app
$app = new Slim($config['slim']);
$container = new SlimContainer($app, $config);

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

    $app->render('setup.html');
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
            $app->log->info(sprintf('New user %s has been created', $user->getEmail()));
            $app->flash('joinSuccess', sprintf('Congrats %s! Now log in and get started!', $user->getEmail()));
            $redirectTo = '/login';
        } catch (\PDOException $p) {
            $app->log->error(sprintf('Database error creating account for %s: %s', $email, $p->getMessage()));
            $app->flash('error', sprintf("Database error creating your account. Stop doing whatever bad thing you're doing!", $email));
            $redirectTo = '/setup';
        } catch (\Exception $e) {
            $app->log->error(sprintf('Error creating account for %s: %s', $email, $e->getMessage()));
            $app->flash('error', $e->getMessage());
            $redirectTo = '/setup';
        }
    } else {
        $app->flash('error', sprintf("'%s' is not a valid email address", $params['email']));
        $redirectTo = '/setup';
    }

    $app->redirect($redirectTo);
});

// Define routes
$app->get('/', function ($page = 1) use ($app, $container) {
    $paginator = $container['zendPaginator'];
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $paginator->getPages(), 'home' => true));
});

$app->get('/page/:page', function ($page = 1) use ($app, $container) {
    $paginator = $container['zendPaginator'];
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);

    $home = ($page == 1) ? true : false;

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $paginator->getPages(), 'home' => $home));
});

$app->get('/day/:day', function($day) use ($app, $container) {
    $image = $container['imageService']->find($day);

    if (!$image) {
        $app->notFound();
    }

    $next = $container['imageService']->findNextImage($day);
    $previous = $container['imageService']->findPreviousImage($day);

    $container['request'] = $app->request;
    $container['image'] = $image;

    $app->render('day.html', array(
        'image' => $image,
        'tags' => $container['metaTags']->getTags(),
        'next' => $next,
        'previous' => $previous,
    ));
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
    $paginator = $container['zendPaginator'];
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);

    $projectDay = $container['imageService']->getProjectDay();
    $daysLeft = 365 - $projectDay;
    $photoCount = $container['imageService']->countImages();
    $percentage = ($photoCount / $projectDay) * 100;

    $viewData = array(
        'paginator' => $paginator,
        'pages' => $paginator->getPages(),
        'projectDay' => $projectDay,
        'photoCount' => $photoCount,
        'percentage' => $percentage,
        'daysLeft' => $daysLeft,
    );

    $app->render('admin/index.html', $viewData);
});

$app->get('/admin/settings', function () use ($app, $container) {
    $user = $container['userService']->getLoggedInUser();
    $app->render('admin/settings.html', array('user' => $user));
});

$app->post('/admin/user', function () use ($app, $container) {
    $user = $container['userService']->getLoggedInUser();
    $params = $app->request()->post();

    $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && ($email != $user->getEmail())) {
        $user->setEmail($email);
        $container['userService']->updateEmail($user);
        $app->log->info(sprintf('Email changed from %s to %s', $user->getEmail(), $email));
        $app->flash('emailSuccess', 'Your email is now ' . $email);
    }

    if ($params['form-type'] == 'change-password' && $params['password']) {
        $app->log->info(sprintf('About to change password for %s', $user->getEmail()));
        try {
            $result = $container['userService']->changePassword($user->getEmail(), $params['password'], $params['new-password'], $params['confirm-password']);
            $app->log->info(sprintf('Password changed for %s', $user->getEmail()));
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
    $photo = new Photo($data);
    try {
        $container['imageService']->save($photo);
        $container['cache']->clearByPrefix($container['paginatorAdapter']::CACHE_KEY_PREFIX);
    } catch (\PDOException $p) {
        $data = json_encode($data);
        if ($p->getCode() == 23000) {
            $app->flash('addPhotoError', "Whoops, something bad happened. Make sure the Day and Photo Id you're adding are unique.");
        } else {
            $app->flash('addPhotoError', "Database error trying to add a photo. Try again?");
        }
        $app->log->error(sprintf('Database error adding a photo with data %s: %s', $data, $p->getMessage()));
    } catch (\Exception $e) {
        $data = json_encode($data);
        $app->flash('addPhotoError', "Error trying to add a photo. Try again?");
        $app->log->error(sprintf('Error adding a photo with data: %s: %s', $data, $e->getMessage()));
    }
    $app->redirect('/admin');
});

$app->post('/admin/delete-photo', function() use ($app, $container) {
    $params = $app->request()->post();
    $photo = new Photo($params);
    $container['imageService']->delete($photo);
    $container['cache']->removeItem($params['photo_id']);
    $container['cache']->clearByPrefix($container['paginatorAdapter']::CACHE_KEY_PREFIX);
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

$app->post('/csp-report', function() use ($app) {
    $app->log->error(sprintf('Referrer: %s', $app->request->getReferrer()));
    $app->log->error(sprintf('User Agent: %s', $app->request->getUserAgent()));
    $app->log->error(trim($app->request()->getBody()));
    $app->halt(200);
});

$app->get('/feed(/:format)', function($format = 'rss') use ($app, $container) {
    $container['baseUrl'] = sprintf('%s%s', $app->request->getUrl(), $app->request->getRootUri());
    $app->response->headers->set('Content-Type', 'application/atom+xml; charset=utf-8');
    $feedWriter = $container['feedWriter'];
    $feedWriter->setView($app->view);

    echo $feedWriter->get($format);
});

$app->get('/logout', function() use ($app, $container) {
    $container['userService']->clearIdentity();
    $app->redirect('/');
});

// Run app
$app->run();
