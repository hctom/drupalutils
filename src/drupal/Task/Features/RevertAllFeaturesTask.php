<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\RevertAllFeaturesTask.
 */

namespace hctom\DrupalUtils\Task\Features;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to revert all features.
 */
class RevertAllFeaturesTask extends FeaturesTask {

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
    return $this->getDrushProcessHelper()
      ->setCommandName('features-revert-all')
      ->setOptions(array(
        'force' => $this->getForce() ? TRUE : FALSE,
      ))
      ->run('Reverted all features', 'Unable to revert all features');
  }

  /**
   * Force revert?
   *
   * @return bool
   *   Whether to revert even if Features module assumes components' state
   *   are default.
   */
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
