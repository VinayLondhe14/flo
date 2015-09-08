<?php

namespace flo\Test\Command\PullRequest;

use flo\Test;
use flo\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DestroyCommandTest extends Test\FunctionalFramework
{

    /**
     * The main flo application.
     *
     * @var string
     */
    private $application;

    /**
     * set up test environment filesystem.
     */
    public function setUp()
    {
        $this->application = new Application();
        parent::setUp();
    }

    /**
     * Test Running pr-destroy with an string instead of PR Number.
     *
     * @expectedException Exception
     * @expectedExceptionMessageRegExp #PR must be a number.#
     */
    public function testStringPRDeploy() {
        $command_run_script = $this->application->find('pr-destroy');
        $command_tester = new CommandTester($command_run_script);
        $command_tester->execute(array(
            'command' => $command_run_script->getName(),
            'pull-request' => "Not-A-Valid-PR",
        ));
    }


    /**
     * Test Running pr-destroy with wrong PR directory path.
     *
     * @expectedException Exception
     * @expectedExceptionMessageRegExp #Pull request directories path does not exist.#
     */

    public function testPrPathDoesNotExist() {
        $command_run_script = $this->application->find('pr-destroy');
        $command_tester = new CommandTester($command_run_script);
        $command_tester->execute(array(
            'command' => $command_run_script->getName(),
            'pull-request' => "2",
        ));
    }
}
