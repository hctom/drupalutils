<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\TaskInterface.
 */

namespace hctom\DrupalUtils\Task;

/**
 * Drupal utilities task interface.
 */
interface TaskInterface {

  /**
   * Return task title.
   *
   * @return string
   *   The human-readable title of the task.
   */
  public function getTitle();

}
