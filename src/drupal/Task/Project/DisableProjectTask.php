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
abstract class DisableProjectTask extends ProjectTask {

  /**
   * {@inheritdoc}
   */
  public function doExecute(InputInterface $input, OutputInterface $output) {
    $isSingleProject = $this->isSingleProject();
    $successMessage = $isSingleProject ? 'Disabled project' : 'Disabled projects';
    $errorMessage = $isSingleProject ? 'Unable to disable project' : 'Unable to disable projects';

    return $this->getDrushProcessHelper()
      ->setCommandName('pm-disable')
      ->setArguments($this->getProjectNames())
      ->mustRun($successMessage, $errorMessage)
      ->getExitCode();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->isSingleProject() ? 'Disable project' : 'Disable projects';
  }

}
