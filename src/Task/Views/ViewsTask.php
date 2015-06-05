<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\ViewsTask.
 */

namespace hctom\DrupalUtils\Task\Views;

use hctom\DrupalUtils\Task\Task;

/**
 * Task command base class for views related tasks.
 */
abstract class ViewsTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function getRequiredModules() {
    return array_merge(parent::getRequiredModules(), array(
      'views',
    ));
  }

}
