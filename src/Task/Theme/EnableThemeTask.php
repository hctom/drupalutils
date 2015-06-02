<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Theme\EnableThemeTask.
 */

namespace hctom\DrupalUtils\Task\Theme;

use hctom\DrupalUtils\Task\Project\EnableProjectTask;

/**
 * Task base class for enabling a theme.
 */
abstract class EnableThemeTask extends EnableProjectTask {

  /**
   * {@inheritdoc}
   */
  public function getProjectNames() {
    return array($this->getTheme());
  }

  /**
   * Return theme name.
   *
   * @return string
   *   The name of the theme to enable.
   */
  abstract public function getTheme();

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Enable "' . $this->getTheme() . '" theme';
  }

}
