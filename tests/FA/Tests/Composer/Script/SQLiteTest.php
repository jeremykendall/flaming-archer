<?php

namespace FA\Tests\Composer\Script;

use Composer\Util\Filesystem;
use FA\Composer\Script\SQLite;

class SQLiteTest extends ComposerScriptTestCase
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Webroot path
     *
     * @var string Webroot path
     */
    protected $rootPath;

    /**
     * Path to db
     *
     * @var string Path to db
     */
    protected $dbFile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->rootPath = APPLICATION_PATH . '/tests/_files/tmp';
        $this->localConfigPath = $this->rootPath . '/config';
        $this->localConfigFile = sprintf('%s/config.php', $this->localConfigPath);

        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists($this->localConfigPath);

        // Copy the application config over to the test directory
        copy(sprintf('%s/config.php', APPLICATION_CONFIG_PATH), $this->localConfigFile);
        copy(
            sprintf('%s/local.dist.php', APPLICATION_CONFIG_PATH), 
            sprintf('%s/local.php', $this->localConfigPath)
        );
        $this->assertFileExists(
            $this->localConfigFile, 
            'Application config was not copied correctly during setup.'
        );
        $this->assertFileExists(
            sprintf('%s/local.php', $this->localConfigPath),
            'Local config was not copied correctly during setup.'
        );

        // Get application config and ensure path to db exists
        $this->applicationConfig = include $this->localConfigFile;
        $this->dbFile = $this->applicationConfig['database'];

        $filesystem->ensureDirectoryExists(pathinfo($this->dbFile, PATHINFO_DIRNAME));
        $filesystem->ensureDirectoryExists($this->rootPath . '/scripts/sql');

        // Copy the sql schema used to create the database
        copy(
            APPLICATION_PATH . '/scripts/sql/schema.sql', 
            $this->rootPath . '/scripts/sql/schema.sql'
        );
        $this->assertFileExists(
            $this->rootPath . '/scripts/sql/schema.sql', 
            'Database schema file was not copied correctly during setup.'
        );

        parent::setUp();

        // Create Composer config
        $this->composerConfig->merge(array('config' => array('vendor-dir' => realpath($this->rootPath) . '/vendor')));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (file_exists($this->rootPath)) {
            $filesystem = new Filesystem();
            $filesystem->removeDirectory($this->rootPath);
        }
        parent::tearDown();
    }

    public function testPrepareNotExists()
    {
        $this->assertFAlse(file_exists($this->dbFile));

        $output = array(
            'Reviewing your Flaming Archer database . . .',
            'Creating new database . . .',
            "Done!"
        );

        // Configure expectations
        foreach ($output as $index => $message) {
            $this->outputMock->expects($this->at($index))
                    ->method('write')
                    ->with($this->equalTo($message), $this->equalTo(true));
        }

        $this->composerMock->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue($this->composerConfig));

        SQLite::prepare($this->event);
        $this->assertTrue(file_exists($this->dbFile));
    }

    public function testPrepareExists()
    {
        // First ensure the database exists
        $config = $this->applicationConfig;

        try {
            $db = new \PDO(
                $config['pdo']['dsn'],
                $config['pdo']['username'],
                $config['pdo']['password'],
                $config['pdo']['options']
            );
            $db->exec(file_get_contents($this->rootPath . '/scripts/sql/schema.sql'));
            $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('images', 'users');")->fetchAll();
            $this->assertEquals(2, count($result));
            $db = null;
        } catch (PDOException $e) {
            throw $e;
        }

        $output = array(
            'Reviewing your Flaming Archer database . . .',
            'Database found.'
        );

        // Configure expectations
        foreach ($output as $index => $message) {
            $this->outputMock->expects($this->at($index))
                    ->method('write')
                    ->with($this->equalTo($message), $this->equalTo(true));
        }

        $this->composerMock->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue($this->composerConfig));

        SQLite::prepare($this->event);
    }

    public function testPDOConnectionIssueThrowsException()
    {
        $this->setExpectedException('\PDOException');

        $output = array(
            'Reviewing your Flaming Archer database . . .',
            'Creating new database . . .',
        );

        // Copy the application config over the test directory
        copy($this->rootPath . '/../config-bad-db.php', $this->localConfigPath . '/config.php');
        $this->assertFileExists($this->localConfigPath . '/config.php', 'Application config was not copied correctly in ' . __METHOD__);

        // Configure expectations
        foreach ($output as $index => $message) {
            $this->outputMock->expects($this->at($index))
                    ->method('write')
                    ->with($this->equalTo($message), $this->equalTo(true));
        }

        $this->composerMock->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue($this->composerConfig));

        SQLite::prepare($this->event);
    }
}
