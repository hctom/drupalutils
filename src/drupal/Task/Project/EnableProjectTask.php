<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Project\EnableProjectTask.
 */

namespace hctom\DrupalUtils\Task\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to enable modules/themes.
 */
class EnableProjectTask extends ProjectTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:project:enable');
  }

  /**
   * {@inheritdoc}
   */
  public function doExecute(InputInterface $input, OutputInterface $output) {
    $projectNames = $this->getProjectNames();

    $process = $this->getDrushProcessHelper()
      ->setCommandName('pm-enable')
      ->setArguments($projectNames)
      ->setOptions(array(
        'resolve-dependencies' => $this->getResolveDependencies(),
        'skip' => $this->getSkipAutomaticDownloading(),
      ))
      ->mustRun('Done', 'Unable to enable project(s)');

    return $process->getExitCode();
  }

  /**
   * Attempt to download any missing dependencies?
   *
   * @return bool
   *   Whether to try to download missing dependencies.
   */
  public function getResolveDependencies() {
    return FALSE;
  }

  /**
   * Skip automatic downloading of libraries?
   *
   * @return bool
   *   Whether to skip automatic downloading of libraries?
   */
  public function getSkipAutomaticDownloading() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Enable projects';
  }

}
