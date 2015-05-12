<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Helper\DrupalHelper;
use hctom\DrupalUtils\Helper\DrushHelper;
use hctom\DrupalUtils\Helper\DrushProcessHelper;
use hctom\DrupalUtils\Helper\FileSystemHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * Base class for all Drupal Utilities commands.
 */
abstract class Command extends SymfonyConsoleCommand {

  /**
   * Return Drupal helper.
   *
   * @return DrupalHelper
   *   The Drupal helper object.
   */
  protected function getDrupalHelper() {
    return $this->getHelper('drupal');
  }

  /**
   * Return Drush helper.
   *
   * @return DrushHelper
   *   The Drush helper object.
   */
  protected function getDrushHelper() {
    return $this->getHelper('drush');
  }

  /**
   * Return Drush process helper.
   *
   * @return DrushProcessHelper
   *   The resetted Drush process helper object.
   */
  protected function getDrushProcessHelper() {
    return $this->getHelper('drush_process')->reset();
  }

  /**
   * Return file system helper.
   *
   * @return FileSystemHelper
   *   The file system helper object.
   */
  protected function getFileSystemHelper() {
    return $this->getHelperSet()->get('filesystem');
  }

  /**
   * Return formatter helper.
   *
   * @return FormatterHelper
   *   The formatter helper object.
   */
  protected function getFormatterHelper() {
    return $this->getHelper('formatter');
  }

  /**
   * Return logger.
   *
   * @return LoggerInterface
   *   The console logger object.
   */
  protected function getLogger() {
    return $this->getApplication()->getLogger();
  }

}
