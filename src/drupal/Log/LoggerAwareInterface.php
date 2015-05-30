<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Log\LoggerAwareInterface.
 */

namespace hctom\DrupalUtils\Log;

/**
 * Should be implemented by classes that depend on a logger.
 */
interface LoggerAwareInterface {

  /**
   * Get logger.
   *
   * @return LoggerInterface
   *   The logger.
   */
  public function getLogger();

  /**
   * Set logger.
   *
   * @param LoggerInterface $logger
   *   The logger.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setLogger(LoggerInterface $logger);

}
