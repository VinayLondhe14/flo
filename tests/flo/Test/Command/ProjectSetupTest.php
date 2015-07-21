<?php

//namespace flo\Test\Command;
//use flo\Console\Application;
//use flo\Test;
//use flo\Test\Command\PullRequest\PullRequestTestHelper;
//use Symfony\Component\Console\Tester\CommandTester;
//use Symfony\Component\Process\Process;
//
//class ProjectSetupTest extends \PHPUnit_Framework_TestCase {

//    public function testCurrentDirectoryException() {
//        $application = new Application();
//        $command_run_script = $application->find('project-setup');
//        $command_tester = new CommandTester($command_run_script);
//        try {
//            $command_tester->execute(array(
//                'command' => $command_run_script->getName()
//            ));
//        } catch(\Exception $e) {
//            $this->assertEquals("You must run project-setup from the git root.", $e->getMessage());
//        }
//    }
//
//    public function testSuccessfulProjectSetup() {
//          var_dump(new Process(chdir('/Users/vlondh200/Workspace/flo')));
//          //$proc = new Process(chdir('../../'));
//          //$proc->run();
//          //var_dump(new Process('pwd'));
//          $application = new Application('flo');
//          $command_run_script = $application->find('project-setup');
//          $command_tester = new CommandTester($command_run_script);
//          $command_tester->execute(array(
//              'command' => $command_run_script->getName(),
//          ));
//          $this->assertEquals("Flo.yml created.\n", $command_tester->getDisplay());
//    }
//}