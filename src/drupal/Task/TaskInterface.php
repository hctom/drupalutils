<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\TaskInterface.
 */

namespace hctom\DrupalUtils\Task;

/**
 * Should be implemented by task command classes.
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
