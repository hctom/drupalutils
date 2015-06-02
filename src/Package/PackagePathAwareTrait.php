<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Output\PackagePathAwareTrait.
 */

namespace hctom\DrupalUtils\Package;

/**
 * Provides methods for working with the composer package path.
 */
trait PackagePathAwareTrait {

  /**
   * The package path.
   *
   * @var string
   */
  private $path;

  /**
   * Return package path.
   *
   * @return string
   *   The package path.
   */
  public function getPackagePath() {
    // No path set.
    if (!$this->path) {
      throw new \RuntimeException('No package path has been set');
    }

    // Does not exist.
    elseif (!file_exists($this->path)) {
      throw new \RuntimeException(sprintf('Specified package path "%s" does not exist', $this->path));
    }

    // No directory.
    elseif (!is_dir($this->path)) {
      throw new \RuntimeException(sprintf('Specified package path "%s" is not a directory', $this->path));
    }

    return $this->path;
  }

  /**
   * Set package path.
   *
   * @param string $path
   *   The package patch.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setPackagePath($path) {
    $this->path = $path;

    return $this;
  }

}
