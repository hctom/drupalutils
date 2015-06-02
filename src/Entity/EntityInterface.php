<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\EntityInterface.
 */

namespace hctom\DrupalUtils\Entity;

/**
 * Should be implemented by entity classes.
 */
interface EntityInterface {

  /**
   * Return unique identifier.
   *
   * @return string
   *   The unigue identifier.
   */
  public function getIdentifier();

}
