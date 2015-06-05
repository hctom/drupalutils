<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\ViewsUiTask.
 */

namespace hctom\DrupalUtils\Task\Views;

/**
 * Task command base class for views UI related tasks.
 */
abstract class ViewsUiTask extends ViewsTask {

  /**
   * {@inheritdoc}
   */
  protected function getRequiredModules() {
    return array_merge(parent::getRequiredModules(), array(
      'views_ui',
    ));
  }

}
