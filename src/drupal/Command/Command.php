<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Helper\DrupalCacheHelper;
use hctom\DrupalUtils\Helper\DrupalHelper;
use hctom\DrupalUtils\Helper\DrupalProjectHelper;
use hctom\DrupalUtils\Helper\DrupalVariableHelper;
use hctom\DrupalUtils\Helper\DrushHelper;
use hctom\DrupalUtils\Helper\DrushProcessHelper;
use hctom\DrupalUtils\Helper\FilesystemHelper;
use hctom\DrupalUtils\Helper\FormatterHelper;
use hctom\DrupalUtils\Helper\HelperSetAwareInterface;
use hctom\DrupalUtils\Helper\TwigHelper;
use hctom\DrupalUtils\Input\InputAwareInterface;
use hctom\DrupalUtils\Log\LoggerInterface;
use hctom\DrupalUtils\Output\OutputAwareInterface;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all Drupal Utilities commands.
 */
abstract class Command extends SymfonyConsoleCommand implements HelperSetAwareInterface {

  /**
   * Return Drupal cache helper.
   *
   * @return DrupalCacheHelper
   *   The Drupal cache helper object.
   */
  protected function getCacheHelper() {
    return $this->getHelper('drupal_cache');
  }

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
   * Return Drupal project helper.
   *
   * @return DrupalProjectHelper
   *   The Drupal project helper object.
   */
  protected function getProjectHelper() {
    return $this->getHelper('drupal_project');
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

  /**
   * Return Drupal variable helper.
   *
   * @return DrupalVariableHelper
   *   The Drupal variable helper object.
   */
  protected function getVariableHelper() {
    return $this->getHelper('drupal_variable');
  }

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);

    // Save input (if needed).
    if ($this instanceof InputAwareInterface) {
      $this->setInput($input);
    }

    // Save output (if needed).
    if ($this instanceof OutputAwareInterface) {
      $this->setOutput($output);
    }
  }

}
