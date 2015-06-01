<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\HelperSetAwareTrait.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\HelperSet;

/**
 * Provides methods for working with the helper set.
 */
trait HelperSetAwareTrait {

  /**
   * Helper set.
   *
   * @var HelperSet
   */
  private $helperSet;

  /**
   * Get helper set.
   *
   * @return HelperSet
   *   The helper set object.
   *
   * @throws \RuntimeException
   */
  public function getHelperSet() {
    if (!$this->helperSet) {
      throw new \RuntimeException('No helper set has been specified');
    }
    return $this->helperSet;
  }

  /**
   * Set helper set.
   *
   * @param HelperSet $helperSet
   *   The helper set object.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setHelperSet(HelperSet $helperSet) {
    $this->helperSet = $helperSet;

    return $this;
  }

}
