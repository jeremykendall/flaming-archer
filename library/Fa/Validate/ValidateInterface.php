<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Validate;

/**
 * Data validation interface
 */
interface ValidateInterface
{
    /**
     * Tests data for validity
     *
     * @param  array $data Data to validate
     * @return bool  True if valid, false if invalid
     */
    public function isValid(array $data);

    /**
     * Gets a list of filtered values
     *
     * @return array Filtered values
     */
    public function getValues();

    /**
     * Gets validation failure messages
     *
     * @return array Failure messages
     */
    public function getMessages();
}
