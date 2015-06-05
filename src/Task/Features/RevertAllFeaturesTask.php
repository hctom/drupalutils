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
    parent::configure();

    $this
      ->setName('task:features:revert:all');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    return $this->getDrushProcessHelper()
      ->setCommandName('features-revert-all')
      ->setArguments($this->getExcludedFeatures())
      ->setOptions(array(
        'force' => $this->getForce() ? TRUE : FALSE,
      ))
      ->mustRun('Reverted all features', 'Unable to revert all features');
  }

  /**
   * Return excluded features.
   *
   * @return array
   *   An array of feature names that should be excluded from revert.
   */
  public function getExcludedFeatures() {
    return array();
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
