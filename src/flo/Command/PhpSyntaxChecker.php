<?php

/**
 * Runs php parallel-lint on change files only.
 */

namespace flo\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Github;


class PhpSyntaxChecker extends Command {

  protected function configure() {
    $this->setName('check-php')
      ->setDescription('runs parallel-lint against the change files.')
      ->addOption(
        'comment',
        null,
        InputOption::VALUE_NONE,
        'If set, the output will be posted to github as a comment on the relevant Pull Request'
      );
  }

  /**
   * Process the check-php command.
   *
   * {@inheritDoc}
   *
   * This command takes in environment variables for knowing what branch to target.
   * If no branch is passed in the environment variable
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $gh_status_post = FALSE;
    $extensions = array(
      'inc',
      'install',
      'module',
      'php',
      'profile',
    );
    $parallel_lint_extensions = implode(',', $extensions);
    $parallel_lint = "./vendor/bin/parallel-lint -e {$parallel_lint_extensions} ";
    $targetBranch = getenv(self::GITHUB_PULL_REQUEST_TARGET_BRANCH);
    $targetRef = getenv(self::GITHUB_PULL_REQUEST_COMMIT);
    $targetURL = getenv(self::JENKINS_BUILD_URL);
    $pullRequest = getenv(self::GITHUB_PULL_REQUEST_ID);
    $github = $this->getGithub();

    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
      $output->writeln("<info>target branch:{$targetBranch}</info>");
      $output->writeln("<info>target ref: {$targetRef}</info>");
      $output->writeln("<info>target URL: {$targetURL}</info>");
      $output->writeln("<info>pull request: {$pullRequest}</info>");
    }

    if (empty($targetBranch)) {
      // Default to master if there is no target branch.
      // You can also change the branch to check against.
      // This checks againts the dev branch:
      // `ghprbTargetBranch=dev flo check-php`
      $targetBranch = 'master';
    }

    // Check if we're going to post to GH or not.
    if (!empty($targetRef) && !empty($targetURL)) {
      // Set the $gh_status_post variable to TRUE if we can post to GH.
      $gh_status_post = TRUE;
    }

    // Get list of files with $extensions to check by running git-diff and
    // filtering by Added (A) and Modified (M).
    $git_extensions = "'*." . implode("' '*.", $extensions) . "'";
    $git_diff_command = "git diff --name-only --no-renames --diff-filter=AM {$targetBranch} -- {$git_extensions}";

    $process = new Process($git_diff_command);
    $process->run();
    $git_diff_output = $process->getOutput();
    // Nothing to check!
    if (empty($git_diff_output)) {
      $output->writeln("<info>No files to check.</info>");
      return;
    }

    // Output some feedback based on verbosity.
    if ($output->getVerbosity() == OutputInterface::VERBOSITY_VERBOSE) {
      $output->writeln("<info>Files about to get parsed:\n{$git_diff_output}</info>");
    }
    elseif ($output->getVerbosity() == OutputInterface::VERBOSITY_VERY_VERBOSE) {
      $output->writeln("<info>About to run:\n{$parallel_lint} | {$git_diff_command}</info>");
    }

    // Run parallel-lint.
    $process = new Process("{$git_diff_command} | {$parallel_lint}");
    $process->run();
    $processOutput = $process->getOutput();

    if (!$process->isSuccessful()) {
      $output->writeln("<error>There is a syntax error.</error>");
      $gh_status_state = 'failure';
      $gh_statue_desc = 'Flo: PHP syntax failure.';
    }
    else {
      $output->writeln("<info>No syntax error found.</info>");
      $gh_status_state = 'success';
      $gh_statue_desc = 'Flo: PHP syntax success.';
    }

    if ($gh_status_post) {
      $output->writeln("<info>Posting to Github Status API.</info>");
      $github->api('repo')->statuses()->create(
        $this->getConfigParameter('organization'),
        $this->getConfigParameter('repository'),
        $targetRef,
        array(
          'state' => $gh_status_state,
          'target_url' => $targetURL,
          'description' => $gh_statue_desc,
          'context' => "flo/phpsyntax",
        )
      );
    }

    // Decide if we're going to post to Github Comment API.
    if ($input->getOption('comment') && !empty($pullRequest) && !$process->isSuccessful()) {
      $output->writeln("<info>Posting to Github Comment API.</info>");

      $github->api('issue')->comments()->create(
        $this->getConfigParameter('organization'),
        $this->getConfigParameter('repository'),
        $pullRequest,
        array('body' => "flo/phpsyntax failure:\n ```\n" .  $processOutput . "```")
      );
    }

    $output->writeln($processOutput);
  }

}
