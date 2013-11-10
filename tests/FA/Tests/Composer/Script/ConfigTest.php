<?php

namespace FA\Tests\Composer\Script;

use FA\Composer\Script\Config;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class ConfigTest extends ComposerScriptTestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    protected $root;

    /**
     * @var string Config dist file name
     */
    protected $distFile;

    /**
     * @var string Config file name
     */
    protected $configFile;

    /**
     * @var array Directory structure
     */
    protected $structure;

    protected function setUp()
    {
        $this->distFile = 'config.user.dist.php';
        $this->configFile = 'config.user.php';
        $this->structure = array(
            'vendor' => array(),
            'config' => array(
                $this->distFile => 'config settings',
            ),
        );
        $this->root = vfsStream::setup('dev.flaming-archer', null, $this->structure);
        $webroot = vfsStream::url('dev.flaming-archer');

        parent::setUp();

        // Replacing default vendor directory with mocked filesystem
        $this->composerConfig->merge(
            array('config' => array('vendor-dir' => $webroot . '/vendor'))
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group vfs
     */
    public function testVirtualFilesystemDirectoryStructure()
    {
        $this->assertTrue($this->root->hasChild('config'));
        $this->assertTrue($this->root->hasChild('config/config.user.dist.php'));
        $this->assertTrue($this->root->hasChild('vendor'));
    }

    public function testCreateConfigNotFound()
    {
        // Confirm mock filesystem doesn't contain config file
        $this->assertFalse($this->root->hasChild('config/' . $this->configFile));

        $output = array(
            'Reviewing your Flaming Archer environment . . .',
            sprintf('Creating %s by copying %s . . .', $this->configFile, $this->distFile),
            sprintf("Done! Please edit %s to begin application setup.", $this->configFile),
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

        $result = Config::create($this->event);
        $this->assertTrue($this->root->hasChild('config/' . $this->configFile));
    }

    public function testCreateConfigFound()
    {
        // Confirm mock filesystem contains config file
        $this->root->addChild(new vfsStreamFile(sprintf('config/%s', $this->configFile)));
        $this->assertTrue($this->root->hasChild(sprintf('config/%s', $this->configFile)));

        $output = array(
            'Reviewing your Flaming Archer environment . . .',
            sprintf('Found %s.', $this->configFile),
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

        $result = Config::create($this->event);
    }
}
