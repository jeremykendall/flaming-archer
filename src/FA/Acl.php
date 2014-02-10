<?php

namespace FA;

use Zend\Permissions\Acl\Acl as ZendAcl;

class Acl extends ZendAcl
{
    public function __construct()
    {
        $this->addRole('guest');
        $this->addRole('admin');

        $this->addResource('/(page/:page)');
        $this->addResource('/day/:day');
        $this->addResource('/feed');
        $this->addResource('/login');
        $this->addResource('/logout');
        $this->addResource('/setup');

        // Admin resources
        $this->addResource('/admin');
        $this->addResource('/admin/');
        $this->addResource('/admin/clear-cache');
        $this->addResource('/admin/feed');
        $this->addResource('/admin/photo');
        $this->addResource('/admin/photo/:day');
        $this->addResource('/admin/photos(/:page)');
        $this->addResource('/admin/settings');
        $this->addResource('/admin/user');

        $this->allow('guest', '/(page/:page)', 'GET');
        $this->allow('guest', '/day/:day', 'GET');
        $this->allow('guest', '/feed', 'GET');
        $this->allow('guest', '/login', array('GET', 'POST'));
        $this->allow('guest', '/setup', array('GET', 'POST'));
        $this->allow('guest', '/logout');

        // admin gets everything
        $this->allow('admin');
        $this->deny('admin', '/login');
        $this->deny('admin', '/setup');
    }
}
