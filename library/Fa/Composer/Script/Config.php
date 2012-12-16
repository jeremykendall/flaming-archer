<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Composer\Script;

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
     * @param  \Composer\Script\Event $event
     */
    public static function create(Event $event)
    {
        $dir = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        $io = $event->getIO();

        $io->write('Reviewing your Flaming Archer environment . . .', true);

        $configExists = file_exists($dir . '/config.php');
        $configDistExists = file_exists($dir . '/config-dist.php');

        if (!$configExists && $configDistExists) {
            $io->write('Creating config.php by copying config-dist.php . . .', true);
            copy($dir . '/config-dist.php', $dir . '/config.php');
            $io->write("Done! Please edit config.php and add your Flickr API key to 'flickr.api.key' and change 'cookie['secret']'.", true);
        } else {
            $io->write('Found config.php.', true);
        }
    }

}
