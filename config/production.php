<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

/**
 * Production config settings
 */
return array(
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'cache_dir' => '/tmp/flaming-archer-cache',
            ),
        ),
    ),
);
