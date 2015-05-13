<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Log\LoggerInterface.
 */

namespace hctom\DrupalUtils\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Should be implemented by classes that should work as a logger.
 */
interface LoggerInterface extends PsrLoggerInterface {

  /**
   * Add a log record at the ALWAYS level.
   *
   * @param string $message
   *   The log message.
   * @param array $context
   *   The log context.
   *
   * @return bool
   *   Whether the record has been processed.
   */
  public function always($message, array $context = array());

}
