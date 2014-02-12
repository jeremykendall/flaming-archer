<?php

namespace FA\Tests;

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
