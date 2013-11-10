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
        $this->distFile = Config::LOCAL_DIST;
        $this->configFile = Config::LOCAL_CONFIG;
        $this->structure = array(
            'vendor' => array(),
            'config' => array(),
        );
        $this->root = vfsStream::setup('dev.flaming-archer', null, $this->structure);
        $this->root->addChild(new vfsStreamFile($this->distFile));
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

    public function testVirtualFilesystemDirectoryStructure()
    {
        $this->assertTrue($this->root->hasChild($this->distFile));
        $this->assertTrue($this->root->hasChild('vendor'));
    }

    public function testCreateConfigNotFound()
    {
        // Confirm mock filesystem doesn't contain config file
        $this->assertFalse($this->root->hasChild($this->configFile));

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
        $this->assertTrue($this->root->hasChild($this->configFile));
    }

    public function testCreateConfigFound()
    {
        // Confirm mock filesystem contains config file
        $this->root->addChild(new vfsStreamFile($this->configFile));
        $this->assertTrue($this->root->hasChild($this->configFile));

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
