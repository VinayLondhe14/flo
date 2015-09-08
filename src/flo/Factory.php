<?php

namespace flo;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;


/**
 * Class Factory.
 *
 * @package flo
 */
class Factory {

  /**
   * Creates a Flo instance.
   *
   * @return Flo
   *   A configured Flo instance.
   */
  private function createFlo() {
    $flo = new Flo();
    // Get config from env variables or files.
    if ($config_env = getenv('FLO')) {
      $config_env = Yaml::parse($config_env);
      $config = new Config($config_env);
    }
    else {
      $fs = new Filesystem();

      $user_config = array();
      $user_config_file = getenv("HOME") . '/.config/flo';
      if ($fs->exists($user_config_file)) {
        $user_config = Yaml::parse($user_config_file);
      }

      $project_config = array();
      $process = new Process('git rev-parse --show-toplevel');
      $process->run();
      if ($process->isSuccessful()) {
        $project_config_file = trim($process->getOutput()) . '/flo.yml';
        if ($fs->exists($project_config_file)) {
          $project_config = Yaml::parse($project_config_file);
        }
      }

      $config = new Config($user_config, $project_config);
    }
    $flo->setConfig($config);

    return $flo;
  }

  /**
   * Creates a Flo instance.
   *
   * @return Flo
   *   A configured Flo instance.
   */
  public static function create() {
    $factory = new static();
    return $factory->createFlo();
  }

}
