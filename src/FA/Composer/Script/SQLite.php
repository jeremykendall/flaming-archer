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
use Zend\Config\Factory as ConfigFactory;

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

        $configPaths = sprintf(
            '%s/config/{,*.}{global,%s,local}.php',
            $root,
            self::getConfigEnvironment($event)
        );
        $config = ConfigFactory::fromFiles(glob($configPaths, GLOB_BRACE));

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

    public static function getConfigEnvironment(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();

        if (isset($extra['configEnvironment'])) {
            return $extra['configEnvironment'];
        } 
        
        if ($event->isDevMode()) {
            return 'development';
        }

        return 'production';
    }
}
