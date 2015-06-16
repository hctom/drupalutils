<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Collection\DataCollectionInterface.
 */

namespace hctom\DrupalUtils\Collection;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * Should be implemented by lazy collection classes.
 */
interface DataCollectionInterface extends Collection, Selectable {

  /**
   * Add multiple elements at the end of the collection.
   *
   * @param array $elements
   *   An array of elements to add.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function addMultiple(array $elements);

}
