<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Console\Terminal\TerminalDimensionsAwareInterface.
 */

namespace hctom\DrupalUtils\Console\Terminal;

/**
 * Should be implemented by classes that depend on the terminal dimensions.
 */
interface TerminalDimensionsAwareInterface {

  /**
   * Return terminal dimensions.
   *
   * @return array
   *   The terminal dimensions as an array containing width and height.
   */
  public function getTerminalDimensions();

  /**
   * Return terminal height.
   *
   * @return int
   *   The terminal height.
   */
  public function getTerminalHeight();

  /**
   * Return terminal width.
   *
   * @return int
   *   The terminal width.
   */
  public function getTerminalWidth();

  /**
   * Set terminal dimensions.
   *
   * @param array $terminalDimensions
   *   The terminal dimensions as an array containing width and height.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setTerminalDimensions($terminalDimensions);

}
