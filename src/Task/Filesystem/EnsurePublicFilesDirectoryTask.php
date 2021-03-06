<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsurePublicFileDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Provides a task command to ensure the public files directory.
 */
class EnsurePublicFilesDirectoryTask extends EnsureDirectoryTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:directory:ensure:public');
  }

  /**
   * {@inheritdoc}
   */
  function getPath() {
    return $this->getDrupalHelper()->getPublicFilesDirectoryPath();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure public files directory';
  }

}
