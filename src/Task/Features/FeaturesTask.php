<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\FeaturesTask.
 */

namespace hctom\DrupalUtils\Task\Features;

use hctom\DrupalUtils\Task\Task;

/**
 * Task command base class for feature related tasks.
 */
abstract class FeaturesTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function getRequiredModules() {
    return array_merge(parent::getRequiredModules(), array(
      'features',
    ));
  }

}
