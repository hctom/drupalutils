<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\FileSystem\EnsurePublicFileDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\FileSystem;

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
