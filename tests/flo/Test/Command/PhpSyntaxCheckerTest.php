<?php

namespace flo\Test\Command;
use flo\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

class PhpSyntaxCheckerTest extends \PHPUnit_Framework_TestCase {
    private $application;

    /**
     * set up test environment filesystem.
     */
    public function setUp() {
        $this->application = new Application();
        parent::setUp();
    }

    public function testNoFilesToCheck() {
        //$output = exec('ghprbTargetBranch=, flo check-php');
        //$this->assertEquals('No files to check.', $output);

        $process = new Process('ghprbTargetBranch=, flo check-php');
        $process->run();
        $output = $process->getOutput();
        $this->assertEquals("No files to check.\n", $output);

//        $command_run_script = $this->application->find('check-php-cs');
//        $command_tester = new CommandTester($command_run_script);
//        $command_tester->execute(array(
//            'command' => $command_run_script->getName()));
//        $this->assertEquals("No files to check.\n", $command_tester->getDisplay());

    }
}