<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\RevertAllFeaturesTask.
 */

namespace hctom\DrupalUtils\Task\Features;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to revert all features.
 */
class RevertAllFeaturesTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:features:revert:all');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if (!$this->getDrupalHelper()->moduleExists('features')) {
      $this->getLogger()->always('<warning>Skipped task: Required {module} module is not enabled</warning>', array(
        'module' => '<code>features</code>',
      ));
    }

    else {
      return $this->getDrushProcessHelper()
        ->setCommandName('features-revert-all')
        ->setOptions(array(
          'force' => $this->getForce() ? TRUE : FALSE,
        ))
        ->run('Reverted all features', 'Unable to revert all features');
    }
  }

  public function getForce() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Revert all features';
  }

}
