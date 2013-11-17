<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

// SQLite database file
$sqlite = realpath(APPLICATION_PATH . '/tests/_files') . '/db/flaming-archer-test.db';

return array(
    'database' => $sqlite,
    'pdo' => array(
        'dsn' => 'sqlite:' . $sqlite,
    ),
    'profile' => array(
        'tagline' => 'tagline',
        'twitter_username' => '@Username',
    ),
);
