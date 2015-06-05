<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\TaskList.
 */

namespace hctom\DrupalUtils\Task;

use Doctrine\Common\Collections\Criteria;
use hctom\DrupalUtils\Collection\Collection;

/**
 * Provides a task list.
 */
class TaskList extends Collection {

  /**
   * {@inheritdoc}
   */
  public function add($element) {
    $this->validateElement($element);

    return parent::add($element);
  }

  /**
   * Validate if element is task object.
   *
   * @param mixed $element
   *   THe element to validate.
   *
   * @throws \InvalidArgumentException
   */
  protected function validateElement($element) {
    if (!$element instanceof TaskInterface) {
      throw new \InvalidArgumentException(sprintf('Invalid task class "%s"', get_class($element)));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function offsetSet($offset, $value) {
    $this->validateElement($value);

    return parent::offsetSet($offset, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function replace(TaskInterface $element) {
    $this->initialize();

    // Build filter criteria.
    $criteria = Criteria::create()
      ->where(Criteria::expr()->eq('name', $element->getName()));

    // Find matches.
    $matches = $this->matching($criteria);

    // No match.
    if ($matches->isEmpty()) {
      throw new \InvalidArgumentException(sprintf('Task "%s" does not exist', $element->getName()));
    }

    // Override matches.
    else {
      foreach ($matches as $key => $match) {
        $this->set($key, $element);
      }
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $value) {
    $this->validateElement($value);

    return parent::set($key, $value);
  }

}
