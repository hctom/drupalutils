<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrushProcessHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Drush\SiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\SiteAliasAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Drupal utilities helper class: Drush process.
 */
class DrushProcessHelper extends ProcessHelper implements SiteAliasAwareInterface, LoggerAwareInterface {

  use LoggerAwareTrait;
  use SiteAliasAwareTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'process.drush';
  }

  /**
   * {@inheritdoc}
   */
  public function run(OutputInterface $output, $cmd, $error = NULL, $callback = NULL, $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE) {
    // Ensure arguments array.
    if (!is_array($cmd)) {
      throw new \RuntimeException('No arguments array passed'); // TODO Exception message
    }

    // No Drush command name given?
    elseif (!isset($cmd['command'])) {
      throw new \RuntimeException('No Drush command given'); // TODO Exception message
    }

    // Build process.
    $processBuilder = ProcessBuilder::create(array_values($cmd));
    $process = $processBuilder->setPrefix(array(
      'drush',
      $this->getSiteAlias(),
    ))->getProcess();

    return parent::run($output, $process, $error, $callback, $verbosity);
  }

}
