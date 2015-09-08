<?php

namespace flo\Test\Command\PullRequest;

use flo\Command\PullRequest\IntegrationCommand;
use flo\Test;
use Symfony\Component\Process\Process;

class IntegrationCommandTest extends \PHPUnit_Framework_TestCase {

  protected function run_protected_method ($obj, $method, $args = array()) {
    $method = new \ReflectionMethod(get_class($obj), $method);
    $method->setAccessible(true);
    return $method->invokeArgs($obj, $args);
  }


  public function testGetCurrentBranch() {
    $obj = new IntegrationCommand();
    $curr_branch = $this->run_protected_method($obj, 'getCurrentHead');
    $process = new Process('git rev-parse --abbrev-ref HEAD');
    $process->run();
    $output = trim($process->getOutput());
    $this->assertEquals($output, $curr_branch);
  }
}
