<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Theme\DisableBartikThemeTask.
 */

namespace hctom\DrupalUtils\Task\Theme;

/**
 * Provides a task command to disable the 'Bartik' theme.
 */
class DisableBartikThemeTask extends DisableThemeTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:theme:disable:bartik');
  }

  /**
   * {@inheritdoc}
   */
  public function getTheme() {
    return 'bartik';
  }

}
