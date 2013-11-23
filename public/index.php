<?php

date_default_timezone_set('UTC');

require '../vendor/autoload.php';

define('APPLICATION_PATH', realpath(dirname(__DIR__)));

if (!defined('SLIM_MODE')) {
    $mode = getenv('SLIM_MODE') ? getenv('SLIM_MODE') : 'production';
    define('SLIM_MODE', $mode);
}

$configPaths = sprintf(
    '%s/config/{,*.}{global,%s,local}.php', 
    APPLICATION_PATH,
    SLIM_MODE
);
$config = Zend\Config\Factory::fromFiles(glob($configPaths, GLOB_BRACE));

use FA\Bootstrap\SlimBootstrap;
use FA\DI\Container;
use FA\Event\FeedEvent;
use FA\Event\PhotoEvent;
use FA\Model\Photo\Photo;
use Slim\Slim;

// Prepare app
$app = new Slim($config['slim']);
$container = new Container($config);
$bootstrap = new SlimBootstrap($app, $container);
$app = $bootstrap->bootstrap();

// Define routes
$app->get('/', function ($page = 1) use ($app, $container) {
    $paginator = $container['zendPaginator'];
    $paginator->setItemCountPerPage($container['config']['pagination']['public.itemCountPerPage']);
    $paginator->setCurrentPageNumber($page);
    $pages = $paginator->getPages();

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $pages, 'home' => true));
});

$app->get('/page/:page', function ($page = 1) use ($app, $container) {
    $paginator = $container['zendPaginator'];
    $paginator->setItemCountPerPage($container['config']['pagination']['public.itemCountPerPage']);
    $paginator->setCurrentPageNumber($page);
    $pages = $paginator->getPages();

    $home = ($page == 1) ? true : false;

    $app->render('index.html', array('paginator' => $paginator, 'pages' => $pages, 'home' => $home));
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

$app->group('/admin', function () use ($app, $container) {

    $app->post('/clear-cache', function() use ($app, $container) {
        $cleared = null;
        $clear = $app->request()->post('clear');

        if ($clear == 1) {
            try {
                $container['cache']->flush()
                $app->flash('cacheSuccess', 'Cache cleared.');
            } catch (\Exception $e) {
                $app->flash('cacheFailure', 'Problem clearing cache!');
                $container['logger.app']->error(
                    sprintf('Exception clearing cache: %s', $e->getMessage())
                );
            }
        }

        $app->redirect('/admin/settings');
    });

    $app->get('(/page/:page)', function ($page = 1) use ($app, $container) {
        $paginator = $container['zendPaginator'];
        $paginator->setItemCountPerPage($container['config']['pagination']['admin.itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);
        $pages = $paginator->getPages();

        $tz = new \DateTimeZone($container['config']['profile']['timezone']);
        $now = new \DateTime('now', $tz);
        $projectDay = $container['imageService']->getProjectDay($now);
        $daysLeft = 365 - $projectDay;
        $photoCount = $container['imageService']->countImages();
        $percentage = ($photoCount / $projectDay) * 100;

        $viewData = array(
            'paginator' => $paginator,
            'pages' => $pages,
            'projectDay' => $projectDay,
            'photoCount' => $photoCount,
            'percentage' => $percentage,
            'daysLeft' => $daysLeft,
        );

        $app->render('admin/index.html', $viewData);
    });

    $app->get('/settings', function () use ($app, $container) {
        $user = $container['userService']->getLoggedInUser();
        $app->render('admin/settings.html', array('user' => $user));
    });

    $app->post('/user', function () use ($app, $container) {
        $user = $container['userService']->getLoggedInUser();
        $params = $app->request()->post();

        $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && ($email != $user->getEmail())) {
            $user->setEmail($email);
            $container['userService']->updateEmail($user);
            $container['logger.app']->info(sprintf('Email changed from %s to %s', $user->getEmail(), $email));
            $app->flash('emailSuccess', 'Your email is now ' . $email);
        }

        if ($params['form-type'] == 'change-password' && $params['password']) {
            $container['logger.app']->info(sprintf('About to change password for %s', $user->getEmail()));
            try {
                $result = $container['userService']->changePassword($user->getEmail(), $params['password'], $params['new-password'], $params['confirm-password']);
                $container['logger.app']->info(sprintf('Password changed for %s', $user->getEmail()));
                $app->flash('passwordSuccess', 'Password changed!');
            } catch (\Exception $e) {
                $container['logger.app']->error(sprintf('Error changing password: %s', $e->getMessage()));
                $app->flash('passwordError', $e->getMessage());
            }
        }

        $app->redirect('/admin/settings');
    });

    $app->post('/photo', function() use ($app, $container) {
        $data = $app->request()->post();
        $photo = new Photo($data);
        try {
            $container['imageService']->save($photo);
            $container['dispatcher']->dispatch('photo.save', new PhotoEvent($photo));
            $container['dispatcher']->dispatch(
                'content.change', 
                new FeedEvent(
                    $container['config']['feed.format'],
                    $container['config']['feed.outfile'],
                    sprintf('%s%s', $c['baseUrl'], $c['feedUri'])
                )
            );
        } catch (\PDOException $p) {
            $data = json_encode($data);
            if ($p->getCode() == 23000) {
                $app->flash(
                    'addPhotoError', 
                    "Whoops, something bad happened. Make sure the Day and Photo Id you're adding are unique."
                );
            } else {
                $app->flash('addPhotoError', "Database error trying to add a photo. Try again?");
            }
            $container['logger.app']->error(sprintf('Database error adding a photo with data %s: %s', $data, $p->getMessage()));
        } catch (\Exception $e) {
            $data = json_encode($data);
            $app->flash('addPhotoError', "Error trying to add a photo. Try again?");
            $container['logger.app']->error(sprintf('Error adding a photo with data: %s: %s', $data, $e->getMessage()));
        }
        $app->redirect('/admin');
    });

    $app->delete('/photo/:day', function($day) use ($app, $container) {
        $photo = $container['imageService']->find($day);
        $container['imageService']->delete($photo);
        $container['dispatcher']->dispatch('photo.delete', new PhotoEvent($photo));
        $container['dispatcher']->dispatch(
            'content.change', 
            new FeedEvent(
                $container['config']['feed.format'],
                $container['config']['feed.outfile'],
                sprintf('%s%s', $container['baseUrl'], $container['feedUri'])
            )
        );
        $app->redirect('/admin');
    });
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

$app->get('/feed', function() use ($app, $container) {
    if (!file_exists(sprintf('%s/public/%s', APPLICATION_PATH, $container['config']['feed.outfile']))) {
        $container['feed.writer']->publish(
            $container['config']['feed.format'], 
            $container['config']['feed.outfile']
        );
        $container['dispatcher']->dispatch(
            'feed.publish', 
            new FeedEvent(
                $container['config']['feed.format'],
                $container['config']['feed.outfile'],
                sprintf('%s%s', $container['baseUrl'], $container['feedUri'])
            )
        );
    }

    $app->response->headers->set('Content-Type', 'application/atom+xml; charset=utf-8');
    echo file_get_contents(APPLICATION_PATH . '/public/feed.xml');
})->name('feed');

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
            $container['logger.app']->info(sprintf('New user %s has been created', $user->getEmail()));
            $app->flash('joinSuccess', sprintf('Congrats %s! Now log in and get started!', $user->getEmail()));
            $redirectTo = '/login';
        } catch (\PDOException $p) {
            $container['logger.app']->error(sprintf('Database error creating account for %s: %s', $email, $p->getMessage()));
            $app->flash('error', sprintf("Database error creating your account. Stop doing whatever bad thing you're doing!", $email));
            $redirectTo = '/setup';
        } catch (\Exception $e) {
            $container['logger.app']->error(sprintf('Error creating account for %s: %s', $email, $e->getMessage()));
            $app->flash('error', $e->getMessage());
            $redirectTo = '/setup';
        }
    } else {
        $app->flash('error', sprintf("'%s' is not a valid email address", $params['email']));
        $redirectTo = '/setup';
    }

    $app->redirect($redirectTo);
});

$app->get('/logout', function() use ($app, $container) {
    $container['userService']->clearIdentity();
    $app->redirect('/');
});

// Run app
$app->run();
