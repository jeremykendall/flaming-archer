<?php

namespace FA\Tests\Composer\Script;

use Composer\Config as ComposerConfig;
use Composer\IO\ConsoleIO;
use Composer\Script\Event;
use FA\Tests\CustomTestCase;

class ComposerScriptTestCase extends CustomTestCase
{
    /**
     * @var Composer\Composer
     */
    protected $composerMock;

    /**
     * @var ComposerConfig
     */
    protected $composerConfig;

    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    protected $outputMock;

    /**
     * @var Composer\Package\Package
     */
    protected $package;

    /**
     * @var Event
     */
    protected $event;

    protected function setUp()
    {
        parent::setUp();

        // Create Composer config
        $this->composerConfig = new ComposerConfig();

        // Set up Composer environment
        $this->composerMock = $this->getMock('Composer\Composer');
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');
        $consoleIO = new ConsoleIO($inputMock, $this->outputMock, $helperMock);
        $this->package = $this->getMock('Composer\Package\PackageInterface');

        $this->event = new Event('post-install-cmd', $this->composerMock, $consoleIO, true);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
