<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Theme\DisableBartikThemeTask.
 */

namespace hctom\DrupalUtils\Task\Theme;

use hctom\DrupalUtils\Task\Project\DisableProjectTask;

/**
 * Provides a task command to disable the 'Bartik' theme.
 */
class DisableBartikThemeTask extends DisableProjectTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:theme:bartik:disable');
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectNames() {
    return array('bartik');
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Disable "Bartik" theme';
  }

}
