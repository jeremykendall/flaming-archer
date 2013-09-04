<?php

namespace FA\Tests\DI;

use FA\DI\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testContainerCreation()
    {
        $config = require APPLICATION_PATH . '/config.dist.php';
        $container = new Container($config);
        $this->assertInstanceOf('Pimple', $container);
    }
}
