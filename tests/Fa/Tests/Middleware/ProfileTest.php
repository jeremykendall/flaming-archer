<?php

namespace Fa\Tests\Middleware;

use Fa\Middleware\Profile;
use Slim\Slim;
use Slim\View as SlimView;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->config = array(
            'profile' => array(
                'brand' => 'Project 365',
                'site_name' => '365 Days of Photography',
                'title' => '365.jeremykendall.net',
                'photographer' => 'Jeremy Kendall',
                'flickr_username' => 'jeremykendall',
                'external_url' => 'http://photography.jeremykendall.net',
            ),
        );
    }

    public function testProfileDataIsAppendedToView()
    {
        $app = new Slim();
        $app->view(new SlimView());
        
        $mw = new Profile($this->config);
        $mw->setApplication($app);
        $mw->setNextMiddleware($app);
        $mw->call();
        
        $data = $app->view()->getData('profile');
        
        $this->assertNotNull($data);
        $this->assertInternalType('array', $data);
        
        $this->assertEquals('jeremykendall', $data['flickr_username']);
        $this->assertEquals('Project 365', $data['brand']);
        $this->assertEquals('365 Days of Photography', $data['site_name']);
        $this->assertEquals('Jeremy Kendall', $data['photographer']);
        $this->assertEquals('365.jeremykendall.net', $data['title']);
    }

    public function testNoDataAppendedIfProfileKeyDoesNotExist()
    {
        $app = new Slim();
        $app->view(new SlimView());
        
        $mw = new Profile(array());
        $mw->setApplication($app);
        $mw->setNextMiddleware($app);
        $mw->call();
        
        $data = $app->view()->getData('profile');
        $this->assertEmpty($data);
    }
}
