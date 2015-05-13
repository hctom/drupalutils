<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Log\Logger.
 */

namespace hctom\DrupalUtils\Log;

use Monolog\Logger as MonologLogger;

/**
 * Provides a logger based on the Monolog logger.
 */
class Logger extends MonologLogger implements LoggerInterface {

  /**
   * Events that should always be logged.
   */
  const ALWAYS = 275;

  /**
   * {@inheritdoc}
   */
  public function __construct($name, array $handlers = array(), array $processors = array()) {
    // Added 'always' level.
    static::$levels[275] = 'ALWAYS';

    parent::__construct($name, $handlers, $processors);
  }

  /**
   * {@inheritdoc}
   */
  public function always($message, array $context = array()) {
    return $this->addRecord(static::ALWAYS, $message, $context);
  }

  /**
   * Add a log record at the ALWAYS level.
   *
   * @param string $message
   *   The log message.
   * @param  array $context
   *   The log context.
   *
   * @return bool
   *   Whether the record has been processed.
   */
  public function addAlways($message, array $context = array()) {
    return $this->addRecord(static::ALWAYS, $message, $context);
  }

}
