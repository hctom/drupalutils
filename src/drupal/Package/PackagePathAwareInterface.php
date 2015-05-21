<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Package\PackagePathAwareInterface.
 */

namespace hctom\DrupalUtils\Package;

/**
 * Should be implemented by classes that depend on the Composer package path.
 */
interface PackagePathAwareInterface {

  /**
   * Return package path.
   *
   * @return string
   *   The package path.
   */
  public function getPackagePath();

  /**
   * Set package path.
   *
   * @param string $path
   *   The package patch.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setPackagePath($path);

}
