<?php

namespace flo\Test\Command;
use flo\Console\Application;
use flo\Test;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectSetupTest extends Test\FunctionalFramework {

    private $application;

    /**
     * set up test environment filesystem.
     */
    public function setUp() {
        $this->application = new Application();
        parent::setUp();
    }


    /**
     * Test Running project-setup inside a git repo but not the root.
     *
     * @expectedException Exception
     * @expectedExceptionMessageRegExp #You must run project-setup from the git root.#
     */
    public function testGitRepoNonRootPRDeploy() {
        $this->fs->mkdir('Test');
        chdir('Test');
        $command_run_script = $this->application->find('project-setup');
        $command_tester = new CommandTester($command_run_script);
        $command_tester->execute(array(
            'command' => $command_run_script->getName(),
        ));
    }

    /**
     * Test Running project-setup outside of a git repo.
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp #Not a git repository.#
     */
    public function testNonGitRootPRDeploy() {
        chdir(sys_get_temp_dir());
        $command_run_script = $this->application->find('project-setup');
        $command_tester = new CommandTester($command_run_script);
        $command_tester->execute(array(
            'command' => $command_run_script->getName(),
        ));
    }

//
    public function testSuccessfulProjectSetup() {
          $command_run_script = $this->application->find('project-setup');
          $command_tester = new CommandTester($command_run_script);
          $command_tester->execute(array(
              'command' => $command_run_script->getName(),
          ));
          $this->assertEquals("Flo.yml created.\n", $command_tester->getDisplay());
    }
}