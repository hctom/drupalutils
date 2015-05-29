<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Project\DisableProjectTask.
 */

namespace hctom\DrupalUtils\Task\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task base class for disabling modules/themes.
 */
abstract class DisableProjectTask extends ProjectTask {

  /**
   * {@inheritdoc}
   */
  protected function doExecute(InputInterface $input, OutputInterface $output) {
    return $this->getProjectHelper()->disable($this->getProjectNames());
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->isSingleProject() ? 'Disable project' : 'Disable projects';
  }

}
