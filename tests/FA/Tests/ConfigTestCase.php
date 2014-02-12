<?php

namespace FA\Tests;

use FA\Tests\FATestCase;
use Zend\Config\Factory as ConfigFactory;

class ConfigTestCase extends FATestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setUpConfig();
    }

    protected function tearDown()
    {
        $this->config = null;
        parent::tearDown();
    }
}
