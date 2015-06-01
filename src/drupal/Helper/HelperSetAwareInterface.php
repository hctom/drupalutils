<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\HelperSetAwareInterface.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\HelperSet;

/**
 * Should be implemented by classes that depend on the helper set.
 */
interface HelperSetAwareInterface {

  /**
   * Get helper set.
   *
   * @return HelperSet
   *   The helper set object.
   *
   * @throws \RuntimeException
   */
  public function getHelperSet();

  /**
   * Set helper set.
   *
   * @param HelperSet $helperSet
   *   The helper set object.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setHelperSet(HelperSet $helperSet);

}
