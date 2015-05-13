<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Cache\ClearCacheTask.
 */

namespace hctom\DrupalUtils\Task\Cache;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities task base class: Clear cache.
 */
abstract class ClearCacheTask extends Task {

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    return $this->getDrushProcessHelper()
      ->setCommandName('cache-clear')
      ->setArguments(array(
        'type' => $this->getType(),
      ))
      ->mustRun('Cleared cache(s)', 'Unable to clear cache(s)')
      ->getExitCode();
  }

  /**
   * @return string
   *   The name of the particular cache to clear. Return 'all' to clear all
   *   caches.
   */
  abstract public function getType();

}
