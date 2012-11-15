<?php

namespace Tsf\Cache\Adapter;

/**
 * Description of Apc
 *
 * @author jkendall
 */
class Apc
{

    /**
     * Fetch a stored variable from the cache
     *
     * @link http://php.net/manual/en/function.apc-fetch.php
     * @param  mixed $key     The key used to store the value. If an array is passed then each element is fetched and returned.
     * @param  bool  $success [optional] True in success or false in failure
     * @return mixed The stored variable or array of variables on success; false on failure
     */
    public function fetch($key, &$success = null)
    {
        return apc_fetch($key, $success);
    }

    /**
     * Cache a variable in the data store
     *
     * @param  string $key
     * @param  mixed  $var
     * @param  int    $ttl
     * @return bool
     */
    public function store($key, $var, $ttl = 0)
    {
        return apc_store($key, $var, $ttl);
    }

    /**
     * Cache a variable in the data store only if it is not already stored
     *
     * @param  string $key
     * @param  mixed  $var
     * @param  int    $ttl
     * @return bool
     */
    public function add($key, $var = null, $ttl = 0)
    {
        return apc_add($key, $var, $ttl);
    }

    /**
     * Clears cache.
     *
     * @param string $cache_type If cache type is 'user', the user cache will
     * be cleared.  Otherwise system cache will be cleared.
     * @return bool
     */
    public function clear($cache_type = null)
    {
        return apc_clear_cache($cache_type);
    }

}
