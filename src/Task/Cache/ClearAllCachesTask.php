<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Cache\ClearAllCachesTask.
 */

namespace hctom\DrupalUtils\Task\Cache;

/**
 * Provides a task command to clear all Drupal caches.
 */
class ClearAllCachesTask extends ClearCacheTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:cache:clear:all');
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Clear all caches';
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'all';
  }

}
