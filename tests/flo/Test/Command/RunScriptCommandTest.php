<?php

namespace flo\Test\Command;

use flo\Console\Application;
use flo\SymfonyOverwrite\Filesystem;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;


class RunScriptCommandTest extends \PHPUnit_Framework_TestCase {

  /**
   * The root project directory.
   *
   * @var string
   */
  private $root;

  /**
   * Filesystem object.
   *
   * @var Filesystem
   */
  private $fs;

  /**
   * Set up test environment filesystem.
   */
  public function setUp() {

    $this->fs = new Filesystem();

    // Attempt to create a temporary directory for the tests and change the
    // current working directory to that directory.
    try {
      $this->root = sys_get_temp_dir() . '/' . str_replace('\\', '-', __CLASS__);
      if ($this->fs->exists($this->root)) {
        $this->fs->remove($this->root);
      }
      $this->fs->mkdir($this->root);
    }
    catch (\Exception $e) {
      $this->tearDown();
      // Throw the exception again so the tests will be skipped.
      throw $e;
    }
    chdir($this->root);

    // Setup a git repo.
    $process = new Process('git init');
    $process->run();
  }

  /**
   * Test running a script with arguments.
   */
  public function testRunScript() {

    $this->writeConfig();

    // Create scripts/post-deploy.sh.
    $post_deploy_script = <<<EOT
#!/usr/bin/env bash
echo "hello $1"
EOT;
    $this->fs->dumpFile($this->root . "/scripts/post-deploy.sh", $post_deploy_script);

    // Run the command.
    $application = new Application();
    $command_run_script = $application->find('run-script');
    $command_tester = new CommandTester($command_run_script);
    $command_tester->execute(array(
      'command' => $command_run_script->getName(),
      'script' => 'post_deploy_cmd',
      'args' => array('world'),
    ));

    // Check the output of the command.
    $this->assertEquals("hello world", trim($command_tester->getDisplay()));
  }

  /**
   * Test that attempting to run an undefined script throws an exception.
   *
   * @expectedException Exception
   * @expectedExceptionMessageRegExp #Could not find script 'invalid'.*#
   */
  public function testRunScriptNotFoundException() {

    $this->writeConfig();

    // Run the command.
    $application = new Application();
    $command_run_script = $application->find('run-script');
    $command_tester = new CommandTester($command_run_script);
    $command_tester->execute(array(
      'command' => $command_run_script->getName(),
      'script' => 'invalid',
      'args' => array('script'),
    ));
  }

  /**
   * Test run-script sh process failing.
   */
  public function testSHScripException() {

    $this->writeConfig();

    // Create scripts/post-deploy.sh.
    $post_deploy_script = <<<EOT
#!/usr/bin/env bash
echo "hello $1"
EOT;
    $this->fs->dumpFile($this->root . "/scripts/post-deploy.sh", $post_deploy_script);

    // Create a Mock Process Object.
    $process = $this->getMockBuilder('Symfony\Component\Process\Process')
      ->disableOriginalConstructor()
      ->getMock();

    // Make sure the isSuccessful method return FALSE so flo throws an exception.
    $process->method('isSuccessful')->willReturn(FALSE);
    $process->method('getErrorOutput')->willReturn('sh failed');

    // Run the command.
    $app = new Application();
    // Set autoExit to false when testing & do not let it catch exceptions.
    $app->setAutoExit(TRUE);
    $app->setCatchExceptions(FALSE);


    $app->setProcess($process);
    $command_run_script = $app->find('run-script');
    $command_tester = new CommandTester($command_run_script);
    $command_tester->execute(array(
      'command' => $command_run_script->getName(),
      'script' => 'post_deploy_cmd',
      'args' => array('world'),
    ));

    // Check the output of the command.
    $this->assertEquals("sh failed", trim($command_tester->getDisplay()));

  }

  /**
   * Helper function to write configuration file.
   */
  private function writeConfig() {
    // Create a sample flo.yml file.
    $project_config = <<<EOT
---
organization: NBCUOTS
repository: Publisher7_nbcuflo
shortname: Publisher7_nbcuflo
github_git_uri: git@github.com:NBCUOTS/Publisher7_nbcuflo.git
pull_request:
  domain: pr.publisher7.com
  prefix: flo-test
scripts:
  pre_deploy_cmd:
  - scripts/pre-deploy.sh
  post_deploy_cmd:
  - scripts/post-deploy.sh
EOT;
    $this->fs->dumpFile($this->root . "/flo.yml", $project_config);
  }

  /**
   * Remove the files and directories created for this test.
   */
  public function tearDown() {
    if ($this->fs->exists($this->root)) {
      $this->fs->remove($this->root);
    }
  }

}
