<?php

namespace FA\Tests\Composer\Script;

use FA\Composer\Script\Config;
use org\bovigo\vfs\vfsStream;

class ConfigTest extends ComposerScriptTestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    protected $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup('dev.flaming-archer');
        vfsStream::create(
            array(
                'config-dist.php' => 'config without secure data', 
                'vendor' => array()
            ), 
            $this->root
        );
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

    public function testCreateConfigNotFound()
    {
        // Confirm mock filesystem doesn't contain config.php
        $this->assertFAlse($this->root->hasChild('config.php'));

        $output = array(
            'Reviewing your Flaming Archer environment . . .',
            'Creating config.php by copying config-dist.php . . .',
            "Done! Please edit config.php and add your Flickr API key to 'flickr.api.key' and change 'cookie['secret']'."
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
        $this->assertTrue($this->root->hasChild('config.php'));
    }
    
    public function testCreateConfigFound()
    {
        // Confirm mock filesystem contains config.php
        vfsStream::create(array('config.php' => 'config without secure data'), $this->root);
        $this->assertTrue($this->root->hasChild('config.php'));

        $output = array(
            'Reviewing your Flaming Archer environment . . .',
            'Found config.php.'
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
