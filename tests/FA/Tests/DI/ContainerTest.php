<?php

namespace FA\Tests\DI;

use FA\DI\Container;
use Slim\Slim;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testContainerCreation()
    {
        $app = new Slim();
        $config = require APPLICATION_PATH . '/config.dist.php';
        $container = new Container($app, $config);
        $this->assertInstanceOf('Pimple', $container);
    }
}
