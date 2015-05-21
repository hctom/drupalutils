<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Helper\DrupalHelper;
use hctom\DrupalUtils\Helper\DrushHelper;
use hctom\DrupalUtils\Helper\DrushProcessHelper;
use hctom\DrupalUtils\Helper\FilesystemHelper;
use hctom\DrupalUtils\Helper\FormatterHelper;
use hctom\DrupalUtils\Helper\TwigHelper;
use hctom\DrupalUtils\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Helper\QuestionHelper;

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
   * @return FilesystemHelper
   *   The file system helper object.
   */
  protected function getFilesystemHelper() {
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

  /**
   * Return question helper.
   *
   * @return QuestionHelper
   *   The question helper object.
   */
  protected function getQuestionHelper() {
    return $this->getHelper('question');
  }

  /**
   * Return Twig templating helper.
   *
   * @return TwigHelper
   *   The Twig templating helper object.
   */
  protected function getTwigHelper() {
    return $this->getHelper('twig');
  }

}
