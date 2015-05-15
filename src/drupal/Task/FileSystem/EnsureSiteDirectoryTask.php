<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureSiteDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Drupal utilities task base class: Ensure site directory.
 */
class EnsureSiteDirectoryTask extends EnsureDirectoryTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:directory:ensure:site');
  }

  /**
   * {@inheritdoc}
   */
  function getPath() {
    return $this->drupal()->getSiteDirectoryPath();
  }


  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure site directory';
  }

}
