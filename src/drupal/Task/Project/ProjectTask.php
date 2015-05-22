<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Project\Projectsask.
 */

namespace hctom\DrupalUtils\Task\Project;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all project tasks.
 */
abstract class ProjectTask extends Task {

  /**
   * Executes the project task.
   *
   * @param InputInterface $input
   *   The input.
   * @param OutputInterface $output
   *   The output.
   *
   * @return null|int
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function doExecute(InputInterface $input, OutputInterface $output) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    if (!$this->getProjectNames()) {
      $this->getLogger()->always('<success>No project given</success>');

      return;
    }

    return $this->doExecute($input, $output);
  }

  /**
   * Return project names.
   *
   * @return array
   *   An array of module/theme names.
   */
  public function getProjectNames() {
    return array('ctools', 'views');
  }

  /**
   * Single project given?
   *
   * @return bool
   *   Whether a single project is given (TRUE) or a list of projects (FALSE).
   */
  protected function isSingleProject() {
    return count($this->getProjectNames()) == 1;
  }

}
