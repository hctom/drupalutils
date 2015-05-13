<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Console\Terminal\TerminalDimensionsAwareTrait.
 */

namespace hctom\DrupalUtils\Console\Terminal;

/**
 * Provides methods for working with the terminal width.
 */
trait TerminalDimensionsAwareTrait {

  /**
   * Terminal dimensions.
   *
   * @var array
   */
  private $terminalDimensions;

  /**
   * Return terminal dimensions.
   *
   * @return array
   *   The terminal dimensions as an array containing width and height.
   */
  public function getTerminalDimensions() {
    return $this->terminalDimensions;
  }

  /**
   * Return terminal height.
   *
   * @return int
   *   The terminal height.
   */
  public function getTerminalHeight() {
    $terminalDimensions = $this->getTerminalDimensions();

    return !empty($terminalDimensions['height']) ? $terminalDimensions['height'] : 0;
  }

  /**
   * Return terminal width.
   *
   * @return int
   *   The terminal width.
   */
  public function getTerminalWidth() {
    $terminalDimensions = $this->getTerminalDimensions();

    return !empty($terminalDimensions['width']) ? $terminalDimensions['width'] : 0;
  }

  /**
   * Set terminal dimensions.
   *
   * @param array $terminalDimensions
   *   The terminal dimensions as an array containing width and height.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setTerminalDimensions($terminalDimensions) {
    $dimensions = array();
    if (!isset($terminalDimensions['width']) && isset($terminalDimensions[0])) {
      $dimensions['width'] = $terminalDimensions[0];
    }

    if (!isset($terminalDimensions['width']) && isset($terminalDimensions[0])) {
      $dimensions['height'] = $terminalDimensions[1];
    }

    $this->terminalDimensions = $dimensions;

    return $this;
  }

}
