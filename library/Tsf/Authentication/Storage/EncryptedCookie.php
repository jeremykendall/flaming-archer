<?php

namespace Tsf\Authentication\Storage;

/**
 * --- Library
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @version $Id$
 */

/**
 * EncryptedCookie class
 * 
 * @category 
 * @package 
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
class EncryptedCookie implements \Zend\Authentication\Storage\StorageInterface
{

    /**
     * Instance of Slim application
     * 
     * @var \Slim\Slim 
     */
    private $app;

    /**
     * Encrypted cookie name
     * 
     * @var string
     */
    private $cookieName;

    /**
     * Duration of cookie
     * 
     * @var int|string 
     */
    private $time = '2 weeks';

    public function __construct(\Slim\Slim $app, $cookieName = 'identity')
    {
        $this->app = $app;
        $this->cookieName = $cookieName;
    }

    /**
     * Returns true if and only if storage is empty
     *
     * @throws Zend_Auth_Storage_Exception If it is impossible to
     *                                     determine whether storage
     *                                     is empty
     * @return boolean
     */
    public function isEmpty()
    {
        if ($this->app->getEncryptedCookie($this->cookieName)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws Zend_Auth_Storage_Exception If reading contents from
     *                                     storage is impossible
     * @return mixed
     */
    public function read()
    {
        $value = $this->app->getEncryptedCookie($this->cookieName);
        return json_decode($value, true);
    }

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws Zend_Auth_Storage_Exception If writing $contents to
     *                                     storage is impossible
     * @return void
     */
    public function write($contents)
    {
        $value = json_encode($contents);
        $this->app->setEncryptedCookie($this->cookieName, $value, $this->time);
    }

    /**
     * Clears contents from storage
     *
     * @throws Zend_Auth_Storage_Exception If clearing contents from
     *                                     storage is impossible
     * @return void
     */
    public function clear()
    {
        $this->app->deleteCookie($this->cookieName);
    }

    /**
     * @param int|string $time The duration of the cookie;
     *                         If integer, should be UNIX timestamp;
     *                         If string, converted to UNIX timestamp with `strtotime`;
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

}
