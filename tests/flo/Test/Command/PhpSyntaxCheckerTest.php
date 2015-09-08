<?php

namespace flo\Test\Command;
use flo\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use flo\Test;
use Symfony\Component\Process\Process;

class PhpSyntaxCheckerTest extends Test\FunctionalFramework {

  /**
   * The main flo application.
   *
   * @var string
   */
  private $application;

  /**
   * set up test environment filesystem.
   */
  public function setUp() {
    $this->application = new Application();
    parent::setUp();
  }

  public function testNoFilesToCheck() {
    $command_run_script = $this->application->find('check-php');
    $command_tester = new CommandTester($command_run_script);
    $command_tester->execute(array(
        'command' => $command_run_script->getName(),
    ));
    $this->assertEquals("No files to check.\n", $command_tester->getDisplay());
  }

}
