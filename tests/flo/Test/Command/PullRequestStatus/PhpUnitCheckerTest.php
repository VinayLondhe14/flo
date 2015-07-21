<?php

namespace flo\Test\Command\PullRequestStatus;


use flo\Console\Application;
use flo\Test\FunctionalFramework;
use Symfony\Component\Console\Tester\CommandTester;


class PhpUnitCheckerTest extends \PHPUnit_Framework_TestCase {

    public function testPhpUnitInIncorrectDirectory() {
        $new_dir = getcwd();
        if(substr(getcwd(), -10) !== '/tests/flo') {
            $new_dir .= '/tests/flo';
        }
        chdir($new_dir);
        $application = new Application();
        $command_run_script = $application->find('check-phpunit');
        $command_tester = new CommandTester($command_run_script);
        $command_tester->execute(array(
            'command' => $command_run_script->getName()));
        $this->assertEquals("phpunit error.\n", $command_tester->getDisplay());
    }

//    public function testPhpUnitInCorrectDirectory() {
//        $root_dir = getcwd();
//        if(substr($root_dir, -10) == '/tests/flo') {
//            $root_dir = substr($root_dir, 0, -10);
//        }
//        $application = new Application();
//        chdir($root_dir);
//        var_dump($root_dir);
//        exec('flo check-phpunit');
//        //$command_run_script = $application->find('check-phpunit');
//        //$command_tester = new CommandTester($command_run_script);
//        //$command_tester->execute(array(
//        //    'command' => $command_run_script->getName()));
//        //$this->assertEquals("phpunit success.\n", $command_tester->getDisplay());
//        $this->assertEquals(true, true);
//    }
}