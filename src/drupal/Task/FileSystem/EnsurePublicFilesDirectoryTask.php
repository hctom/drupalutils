<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsurePublicFileDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Drupal utilities task base class: Ensure public files directory.
 */
class EnsurePublicFilesDirectoryTask extends EnsureDirectoryTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:directory:ensure:files');
  }

  /**
   * {@inheritdoc}
   */
  function getPath() {
    return $this->drupal()->getFilesDirectoryPath();
  }


  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure files directory';
  }

}
