<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Helper\DrupalHelper;
use hctom\DrushWrapper\Helper\DrushHelper;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * Drupal utilities command base class.
 */
abstract class Command extends SymfonyConsoleCommand {

  /**
   * Return Drupal helper.
   *
   * @return DrupalHelper
   *   The Drupal helper object.
   */
  public function drupal() {
    return $this->getHelper('drupal');
  }

  /**
   * Return Drush helper.
   *
   * @return DrushHelper
   *   The Drush helper object.
   */
  public function drush() {
    return $this->getHelper('drush');
  }

  /**
   * Return formatter helper.
   *
   * @return FormatterHelper
   *   The formatter helper object.
   */
  public function formatter() {
    return $this->getHelper('formatter');
  }

}
