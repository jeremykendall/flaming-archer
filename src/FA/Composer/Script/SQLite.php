<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA\Composer\Script;

use Composer\Script\Event;

/**
 * SQLite class
 *
 * Ensures database and schema exist
 */
class SQLite
{
    /**
     * Checks for database and configures database if it does not exist
     *
     * @param  Event        $event
     * @throws PDOException
     */
    public static function prepare(Event $event)
    {
        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));
        $config = include $root . '/config/config.php';

        $io = $event->getIO();

        $io->write('Reviewing your Flaming Archer database . . .', true);

        $dbExists = file_exists($config['database']);

        if (!$dbExists) {
            try {
                $io->write('Creating new database . . .', true);
                $db = new \PDO(
                    $config['pdo']['dsn'],
                    $config['pdo']['username'],
                    $config['pdo']['password'],
                    $config['pdo']['options']
                );
                $db->exec(file_get_contents($root . '/scripts/sql/schema.sql'));
                $db = null;
            } catch (\PDOException $e) {
                throw $e;
            }
            $io->write("Done!", true);
        } else {
            $io->write('Database found.', true);
        }
    }
}
