<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\FeaturesTask.
 */

namespace hctom\DrupalUtils\Task\Features;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Task command base class for feature related tasks.
 */
abstract class FeaturesTask extends Task {

  /**
   * Return revertable features.
   *
   * @return array
   *   An array of names of features that need to be reverted.
   *
   * @throws RuntimeException
   */
  public function getRevertableFeatures() {
    $process = $this->getDrushProcessHelper()
      ->setCommandName('drupalutils-features-revertable')
      ->mustRun(NULL, 'Unable to determine revertable features', FALSE);

    // Unable to parse revertable features.
    if (($features = json_decode($process->getOutput())) === NULL) {
      throw new RuntimeException('Unable to parse revertable features');
    }

    return $features;
  }

  /**
   * {@inheritdoc}
   */
  protected function getRequiredModules() {
    return array_merge(parent::getRequiredModules(), array(
      'features',
    ));
  }

}
