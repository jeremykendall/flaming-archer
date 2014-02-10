<?php

namespace FA\Tests;

use FA\Acl;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Acl
     */
    private $acl;

    protected function setUp()
    {
        parent::setUp();
        $this->acl = new Acl();
    }

    protected function tearDown()
    {
        $this->acl = null;
        parent::tearDown();
    }

    public function testResourcesExist()
    {
        $expected = array (
            '/(page/:page)',
            '/day/:day',
            '/feed',
            '/login',
            '/logout',
            '/setup',
            '/admin',
            '/admin/',
            '/admin/clear-cache',
            '/admin/feed',
            '/admin/photo',
            '/admin/photo/:day',
            '/admin/photos(/:page)',
            '/admin/settings',
            '/admin/user',
        );

        $resources = $this->acl->getResources();

        asort($expected);
        asort($resources);

        $this->assertEquals($expected, $resources);
    }

    /**
     * @dataProvider aclData
     */
    public function testPermissions($allowed, $role, $resource, $privilege)
    {
        $this->assertEquals($allowed, $this->acl->isAllowed($role, $resource, $privilege));
    }

    public function aclData()
    {
        return array(
            array(true, 'guest', '/(page/:page)', 'GET'),
            array(true, 'guest', '/login', 'POST'),
            array(false, 'admin', '/login', null),
        );
    }
}
