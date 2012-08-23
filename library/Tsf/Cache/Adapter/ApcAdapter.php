<?php

namespace Tsf\Cache\Adapter;

/**
 * --- Library
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * ApcAdapter class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class ApcAdapter
{

    /**
     * Cache a variable in the data store
     * 
     * @param string $key Store the variable using this name. keys are
     * cache-unique, so storing a second value with the same
     * key will overwrite the original value.
     * @param mixed $var The variable to store
     * @param int $ttl [optional] Time To Live; store var in the cache for
     * ttl seconds. After the ttl has passed, the stored variable will be
     * expunged from the cache (on the next request). If no ttl
     * is supplied (or if the ttl is
     * 0), the value will persist until it is removed from
     * the cache manually, or otherwise fails to exist in the cache (clear,
     * restart, etc.).
     * 
     * @return bool true on success or false on failure.
     * Second syntax returns array with error keys.
     */
    public function store($key, $var, $ttl)
    {
        return null;
    }

    /**
     * Fetch a stored variable from the cache
     * 
     * @param mixed $key The key used to store the value.  If an array is passed then each
     * element is fetched and returned.
     * @param bool $success [optional]. Set to true in success and false in failure.
     * 
     * @return mixed The stored variable or array of variables on success; false on failure
     */
    public function fetch($key, $success = null)
    {
        return null;
    }

}
