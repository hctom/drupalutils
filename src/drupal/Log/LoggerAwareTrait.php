<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Log\LoggerAwareTrait.
 */

namespace hctom\DrupalUtils\Log;

/**
 * Provides methods for working with a logger.
 */
trait LoggerAwareTrait {

  /**
   * The logger.
   *
   * @var LoggerInterface
   */
  private $logger;

  /**
   * Get logger.
   *
   * @return LoggerInterface
   *   The logger.
   *
   * @throws \RuntimeException
   */
  public function getLogger() {
    if (!$this->logger) {
      throw new \RuntimeException('No logger has been specified');
    }

    return $this->logger;
  }

  /**
   * Set logger.
   *
   * @param LoggerInterface $logger
   *   The logger.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;

    return $this;
  }

}
