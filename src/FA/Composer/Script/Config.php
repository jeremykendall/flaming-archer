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
 * Config class
 *
 * Ensures config file exists
 */
class Config
{
    /**
     * Creates config file if it does not already exist
     *
     * @param Event $event
     */
    public static function create(Event $event)
    {
        $dir = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        $io = $event->getIO();

        $io->write('Reviewing your Flaming Archer environment . . .', true);

        $configExists = file_exists($dir . '/config/config.user.php');
        $configDistExists = file_exists($dir . '/config/config.user.dist.php');

        if (!$configExists && $configDistExists) {
            $io->write('Creating config.user.php by copying config.user.dist.php . . .', true);
            copy($dir . '/config/config.user.dist.php', $dir . '/config/config.user.php');
            $io->write("Done! Please edit config.user.php to begin application setup.", true);
        } else {
            $io->write('Found config.user.php.', true);
        }
    }
}
