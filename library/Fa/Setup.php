<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa;

use Composer\Script\Event;

/**
 * Setup
 *
 * Performs application setup tasks. Intended to be called by Composer
 */
class Setup
{

    public static function createConfig(Event $event)
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
            return true;
        }
        
        $io->write('Found config.php.', true);
        return true;
    }

    public static function prepareDatabase(Event $event)
    {
        return null;
    }

}
