<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Project\DisableProjectTask.
 */

namespace hctom\DrupalUtils\Task\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to disable modules/themes.
 */
class DisableProjectTask extends ProjectTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:project:disable');
  }

  /**
   * {@inheritdoc}
   */
  public function doExecute(InputInterface $input, OutputInterface $output) {
    $projectNames = $this->getProjectNames();

    return $this->getDrushProcessHelper()
      ->setCommandName('pm-disable')
      ->setArguments($projectNames)
      ->mustRun('Done', 'Unable to uninstall project(s)')
      ->getExitCode();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Disable projects';
  }

}
