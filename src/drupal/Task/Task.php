<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Task.
 */

namespace hctom\DrupalUtils\Task;

use hctom\DrupalUtils\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for Drupal utilities task commands.
 */
abstract class Task extends Command implements TaskInterface {

  /**
   * {@inheritdoc}
   */
  public function run(InputInterface $input, OutputInterface $output) {
    foreach ($this->getRequiredModules() as $moduleName) {
      if (!$this->getDrupalHelper()->moduleExists($moduleName)) {
        $this->getLogger()->always('<warning>Skipped task: Required {module} module is not enabled</warning>', array(
          'module' => '<code>features</code>',
        ));

        return;
      }
    }

    return parent::run($input, $output);
  }

  /**
   * Return required modules.
   *
   * @return array
   *   An array of module names describing all modules that are required to run
   *   this task.
   */
  protected function getRequiredModules() {
    return array();
  }

}
