<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\FileSystem\EnsureSiteDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\FileSystem;

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
