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
    // Task should be skipped.
    if (($message = $this->skipWithMessage())) {
      $this->getLogger()->always('<warning>Skipped task: {message}</warning>', array(
        'message' => $message,
      ));

      return;
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

  /**
   * Task should be skipped with a message?
   *
   * @return string|bool|null
   *   The message to display. Return FALSE or NULL to not skip the task.
   */
  protected function skipWithMessage() {
    $requiredModules = $this->getRequiredModules();

    if (count($requiredModules)) {
      foreach ($requiredModules as $moduleName) {
        if (!$this->getProjectHelper()->isEnabled($moduleName)) {
          return sprintf('Required %s module is not enabled', $this->getFormatterHelper()->formatInlineCode($moduleName));
        }
      }
    }
  }

}
