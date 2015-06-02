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
 * Task command base class to clear Drupal caches.
 */
abstract class ClearCacheTask extends Task {

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    return $this->getCacheHelper()->flush($this->getType());
  }

  /**
   * Return cache type.
   *
   * @return string
   *   The name of the particular cache to clear. Return 'all' to clear all
   *   caches.
   */
  abstract public function getType();

}
