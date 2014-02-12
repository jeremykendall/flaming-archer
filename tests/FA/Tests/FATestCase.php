<?php

namespace FA\Tests;

use Zend\Config\Factory as ConfigFactory;

class FATestCase extends \PHPUnit_Framework_TestCase
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
    }

    protected function tearDown()
    {
        $this->config = null;
        $this->db = null;
        parent::tearDown();
    }

    protected function setUpConfig()
    {
        $paths = sprintf(
            '%s/config/{,*.}{global,%s,local}.php',
            APPLICATION_PATH,
            SLIM_MODE
        );

        $this->config = ConfigFactory::fromFiles(glob($paths, GLOB_BRACE));
    }

    /**
     * Currently used by SQLiteTest
     */
    protected function setUpDbOnFilesystem()
    {
        $this->setUpConfig();

        try {
            $db = new \PDO(
                $this->config['pdo']['dsn'],
                $this->config['pdo']['username'],
                $this->config['pdo']['password'],
                $this->config['pdo']['options']
            );
            $this->seedDatabase($db);
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    protected function setUpDbInMemory()
    {
        $dsn = 'sqlite::memory:';
        $options = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        );

        try {
            $this->db = new \PDO($dsn, null, null, $options);
            $this->seedDatabase($this->db);
        } catch (\PDOException $p) {
            die(sprintf('DB connection error: %s', $p->getMessage()));
        }
    }

    protected function seedDatabase(\PDO $db)
    {
        $scripts = array(
            APPLICATION_PATH . '/scripts/sql/schema.sql',
            APPLICATION_PATH . '/scripts/sql/seed_data.sql',
            APPLICATION_PATH . '/scripts/sql/migration/0001.sql',
            APPLICATION_PATH . '/scripts/sql/migration/0002.sql',
        );

        foreach ($scripts as $sql) {
            $db->exec(file_get_contents($sql));
        }
    }
}
