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
    const LOCAL_DIST = 'config/local.dist.php';
    const LOCAL_CONFIG = 'config/local.php';
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

        $dist = sprintf('%s/%s', $dir, self::LOCAL_DIST);
        $config = sprintf('%s/%s', $dir, self::LOCAL_CONFIG);

        if (!file_exists($config) && file_exists($dist)) {
            $message = sprintf(
                'Creating %s by copying %s . . .', 
                self::LOCAL_CONFIG, 
                self::LOCAL_DIST
            ); 
            $io->write($message, true);
            copy($dist, $config);
            $io->write(
                sprintf('Done! Please edit %s to begin application setup.', self::LOCAL_CONFIG),
                true
            );
        } else {
            $io->write(sprintf('Found %s.', self::LOCAL_CONFIG), true);
        }
    }
}
