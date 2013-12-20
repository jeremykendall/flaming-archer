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
            '/',
            '/day/:day',
            '/feed',
            '/login',
            '/logout',
            '/page/:page',
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

    public function testGuestPermissions()
    {
        $this->markTestSkipped();
    }

    public function testAdminPermissions()
    {
        $this->markTestSkipped();
    }
}
