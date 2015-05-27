<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Project\EnableProjectTask.
 */

namespace hctom\DrupalUtils\Task\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task base class for enabling modules/themes.
 */
abstract class EnableProjectTask extends ProjectTask {

  /**
   * {@inheritdoc}
   */
  protected function doExecute(InputInterface $input, OutputInterface $output) {
    $isSingleProject = $this->isSingleProject();
    $successMessage = $isSingleProject ? 'Enabled project' : 'Enabled projects';
    $errorMessage = $isSingleProject ? 'Unable to enable project' : 'Unable to enable projects';

    return $this->getDrushProcessHelper()
      ->setCommandName('pm-enable')
      ->setArguments($this->getProjectNames())
      ->setOptions(array(
        'resolve-dependencies' => $this->getResolveDependencies(),
        'skip' => $this->getSkipAutomaticDownloading(),
      ))
      ->mustRun($successMessage, $errorMessage);
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
    return $this->isSingleProject() ? 'Enable project' : 'Enable projects';
  }

}
