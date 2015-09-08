<?php

namespace flo\Test\Command\Deployments;

use Github;
use flo\Test\Command\PullRequest\PullRequestTestHelper;
use Symfony\Component\Console\Tester\CommandTester;


class TagDeployTest extends PullRequestTestHelper
{
  /**
   * Test Running tag-release.
   */
  public function testCreatingBadRelease() {
    $this->writeConfig();

    // Now after ALLLLL that set up, lets call our command
    $command_run_script = $this->application->find('tag-deploy');

    $command_tester = new CommandTester($command_run_script);
    $command_tester->execute(array(
        'command' => $command_run_script->getName(),
        'env' => "",
        'tag' => "1.0.0",
        '--pre-release' => true,
    ));

    $this->assertEquals("You must have your acquia username/password/subscription configured in your flo config to run deploys.\n", $command_tester->getDisplay());
  }
}
