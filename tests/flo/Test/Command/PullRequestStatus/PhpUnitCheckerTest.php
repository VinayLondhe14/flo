<?php

namespace flo\Test\Command\PullRequestStatus;


use flo\Console\Application;
use flo\Test\FunctionalFramework;
use Symfony\Component\Console\Tester\CommandTester;


class PhpUnitCheckerTest extends FunctionalFramework {

  private $application;

  public function setUp() {
    $this->application = new Application();
    parent::setUp();
  }

  public function testPhpUnitInIncorrectDirectory() {
    $command_run_script = $this->application->find('check-phpunit');
    $command_tester = new CommandTester($command_run_script);
    $command_tester->execute(array(
        'command' => $command_run_script->getName()));
    $this->assertEquals("phpunit error.\n", $command_tester->getDisplay());
  }
}
