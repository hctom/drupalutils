<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Process\DrushProcessBuilder.
 */

namespace hctom\DrupalUtils\Process;

use hctom\DrupalUtils\Console\Application;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareTrait;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Drupal utilities Drush process builder class.
 */
class DrushProcessBuilder implements DrushSiteAliasAwareInterface {

  use DrushSiteAliasAwareTrait;

  /**
   * Drush process arguments.
   *
   * @var array
   */
  private $arguments;

  /**
   * Drush process options.
   *
   * @var array
   */
  private $options;

  /**
   * Constructor.
   *
   * @param string|null $drushSiteAlias
   *   An optional Drush site alias to use (defaults to '@none').
   */
  public function __construct($drushSiteAlias = NULL) {
    // Set site alias (if given)
    if (isset($siteAlias)) {
      $this->setDrushSiteAlias($drushSiteAlias);
    }
  }

  /**
   * Return Drush process arguments.
   *
   * @param bool $processed
   *   Whether to process arguments before returning (defaults to FALSE).
   *
   * @return array
   *   The Drush process arguments.
   */
  public function getArguments($processed = FALSE) {
    if (empty($this->arguments)) {
      return array();
    }

    if ($processed) {
      $arguments = $this->getArguments();

      // Rewrite command name (if needed).
      if (!empty($arguments['command']) && preg_match('/^drush:/', $arguments['command'])) {
        $arguments['command'] = substr($arguments['command'], strlen('drush:'));
      }

      // Filter empty arguments.
      foreach ($arguments as $argumentName => $argumentValue) {
        if (empty($argumentValue) && $argumentValue !== 0) {
          unset($arguments[$argumentName]);
        }
      }

      return $arguments;
    }

    return $this->arguments;
  }

  /**
   * Return Drush process.
   *
   * @return Process
   *   The Drush process.
   */
  public function getProcess() {
    $processBuilder = new ProcessBuilder();

    // Build process.
    $process = $processBuilder
      ->setPrefix(array(
        'drush',
        $this->getDrushSiteAlias(),
      ))
      ->setArguments(array_merge(array_values($this->getArguments(TRUE)), array_values($this->getOptions(TRUE))))
      ->getProcess();

    return $process;
  }

  /**
   * Return Drush process options.
   *
   * @param bool $processed
   *   Whether to process options before returning (defaults to FALSE).
   *
   * @return array
   *   The Drush process options.
   */
  public function getOptions($processed = FALSE) {
    if (empty($this->options)) {
      return array();
    }

    if ($processed) {
      $options = $this->getOptions();

      foreach ($options as $optionName => &$optionValue) {
        // Filter empty options.
        if (empty($optionValue) && $optionValue !== 0) {
          unset($options[$optionName]);
        }

        // Build option string.
        else {
          $optionValue = (!preg_match('/^--/', $optionName) ? '--' : '') . $optionName . ($optionValue !== TRUE ? '=' . $optionValue : '');
        }
      }

      // Remove '--drush-site-alias' option (only used internal).
      unset($options[Application::INPUT_OPTION_DRUSH_SITE_ALIAS]);

      return $options;
    }

    return $this->options;
  }

  /**
   * Set Drush process arguments.
   *
   * @param array $arguments
   *   The arguments to pass to the Drush process.
   *
   * @return $this
   *   A self-reference for method chaining.
   */
  public function setArguments(array $arguments = array()) {
    $this->arguments = $arguments;

    return $this;
  }

  /**
   * Set Drush process options.
   *
   * @param array $options
   *   The options to pass to the Drush process.
   *
   * @return $this
   *   A self-reference for method chaining.
   */
  public function setOptions(array $options = array()) {
    $this->options = $options;

    return $this;
  }

}
