<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\TaskListInterface.
 */

namespace hctom\DrupalUtils\Task;

use hctom\DrupalUtils\Collection\DataCollectionInterface;

/**
 * Should be implemented by task list classes.
 */
interface TaskListInterface extends DataCollectionInterface {


  /**
   * Replace task list element.
   *
   * @param TaskInterface $element
   *   A task object.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function replace(TaskInterface $element);

}
