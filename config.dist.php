<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

/**
 * EDIT THE ITEMS IN THE $userConfig ARRAY TO COMPLETE APPLICATION CONFIGURATION
 *
 * Remember, you must have a Flickr API key in order to use Flaming Archer
 */
$userConfig = array(
    'flickr.api.key' => '@@@ Your Flickr API key @@@',
    // Change these settings to whatever you like
    'profile' => array(
        'brand' => 'Flaming Archer',
        'site_name' => '365 Days of Photography',
        'flickr_username' => '@@@ Your Flickr username @@@',
        'photographer' => '@@@ Your name @@@',
        'tagline' => "@@@ Some clever tagline @@@",
        'external_url' => '@@@ Website, Flickr profile, blog, etc. @@@',
    ),
    // Change this to a random-ish string. It's used for encrypting cookies.
    'cookies.secret_key' => 'CHANGE_ME',
);

// Slim configuration
$slim = array(
    'templates.path' => __DIR__ . '/templates',
    'log.level' => Slim\Log::ERROR,
    'log.enabled' => true,
    'log.writer' => new Slim\Extras\Log\DateTimeFileWriter(
        array(
            'path' => __DIR__ . '/logs',
            'name_format' => 'Y-m-d'
        )
    ),
    // Global, not SessionCookie, cookie settings
    'cookies.secret_key' => $userConfig['cookies.secret_key'],
    'cookies.encrypt' => false, // must be false until https://github.com/codeguy/Slim/pull/606 is merged
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,
);

// SQLite database file
$sqlite = __DIR__ . '/db/flaming-archer.db';

$config = array(
    'slim' => $slim,
    'twig' => array(
        'environment' => array(
            'charset' => 'utf-8',
            'cache' => realpath(__DIR__ . '/templates/cache'),
            'auto_reload' => true,
            'strict_variables' => true,
            'autoescape' => true,
        ),
    ),
    'session_cookies' => array(
        'expires' => '20 minutes',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => false,
        'name' => 'slim_session',
        'secret' => $userConfig['cookies.secret_key'],
        'cipher' => $slim['cookies.cipher'],
        'cipher_mode' => $slim['cookies.cipher_mode'],
    ),
    'database' => $sqlite,
    'pdo' => array(
        'dsn' => 'sqlite:' . $sqlite,
        'username' => null,
        'password' => null,
        'options' => array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    ),
    // All cache config happens here, so you can alter these settings to use
    // whichever of the Zend\Cache adapters and settings you like.
    // http://framework.zend.com/manual/2.0/en/index.html#zend-cache
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'ttl' => 60 * 60 * 24, // One day
                'namespace' => 'flaming-archer',
                'cache_dir' => realpath('../tmp')
            )
        ),
        'plugins' => array(
            'ExceptionHandler' => array(
                'throw_exceptions' => false
            ),
            'Serializer'
        ),
    ),
    'login.url' => '/login',
    'secured.urls' => array(
        array('path' => '/admin'),
        array('path' => '/admin/.+')
    ),
);

return array_merge($userConfig, $config);
