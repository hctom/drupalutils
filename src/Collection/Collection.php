<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Collection\Collection.
 */

namespace hctom\DrupalUtils\Collection;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Base class for collections.
 */
abstract class Collection extends AbstractLazyCollection implements CollectionInterface {

  /**
   * Add an element at the end of the collection.
   *
   * @param mixed $element
   *   The element to add.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function add($element) {
    parent::add($element);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addMultiple(array $elements) {
    foreach ($elements as $element) {
      $this->add($element);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  protected function doInitialize() {
    $this->collection = new ArrayCollection(array());
  }

  /**
   * {@inheritdoc}
   */
  public function matching(Criteria $criteria) {
    $this->initialize();

    return $this->collection->matching($criteria);
  }

}
