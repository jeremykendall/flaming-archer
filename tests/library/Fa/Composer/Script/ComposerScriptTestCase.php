<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComposerScriptTestCase
 *
 * @author jkendall
 */
class ComposerScriptTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Composer\Composer
     */
    protected $composerMock;

    /**
     * @var \Composer\Config
     */
    protected $composerConfig;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $outputMock;

    /**
     * @var \Composer\Script\Event
     */
    protected $event;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // Create Composer config
        $this->composerConfig = new \Composer\Config();

        // Set up Composer environment
        $this->composerMock = $this->getMock('Composer\Composer');
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');
        $consoleIO = new \Composer\IO\ConsoleIO($inputMock, $this->outputMock, $helperMock);
        $this->event = new \Composer\Script\Event('post-install-cmd', $this->composerMock, $consoleIO, true);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

}
