<?php

namespace FA\Tests\Dao;

use PDO;

class CommonDbTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    protected function setUp()
    {
        parent::setUp();
        $dsn = 'sqlite::memory:';
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        try {
            $this->db = new PDO($dsn, null, null, $options);
        } catch (PDOException $p) {
            die(sprintf('DB connection error: %s', $p->getMessage()));
        }

        try {
            $this->db->exec(file_get_contents(APPLICATION_PATH . '/scripts/sql/schema.sql'));
            $this->db->exec(file_get_contents(APPLICATION_PATH . '/scripts/sql/seed_data.sql'));
            $this->db->exec(file_get_contents(APPLICATION_PATH . '/scripts/sql/migration/0001.sql'));
            $this->db->exec(file_get_contents(APPLICATION_PATH . '/scripts/sql/migration/0002.sql'));
        } catch (PDOException $p) {
            die(sprintf('DB setup error: %s', $p->getMessage()));
        }
    }

    protected function tearDown()
    {
        $this->db = null;
        parent::tearDown();
    }
}
