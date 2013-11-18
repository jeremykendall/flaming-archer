<?php

namespace FA\Tests;

use Zend\Config\Factory as ConfigFactory;

class CustomTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var PDO
     */
    protected $db;

    protected function setUp()
    {
        parent::setUp();

        $paths = sprintf(
            '%s/config/{,*.}{global,%s,local}.php',
            APPLICATION_PATH,
            SLIM_MODE
        );

        $this->config = ConfigFactory::fromFiles(glob($paths, GLOB_BRACE));
    }

    protected function tearDown()
    {
        $this->config = null;
        parent::tearDown();
    }

    protected function createDb()
    {
        try {
            $db = new \PDO(
                $this->config['pdo']['dsn'],
                $this->config['pdo']['username'],
                $this->config['pdo']['password'],
                $this->config['pdo']['options']
            );
            $db->exec(file_get_contents(APPLICATION_PATH . '/scripts/sql/schema.sql'));
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
