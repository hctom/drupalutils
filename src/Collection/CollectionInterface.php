<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Collection\CollectionInterface.
 */

namespace hctom\DrupalUtils\Collection;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * Should be implemented by collection classes.
 */
interface CollectionInterface extends Collection, Selectable {

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
