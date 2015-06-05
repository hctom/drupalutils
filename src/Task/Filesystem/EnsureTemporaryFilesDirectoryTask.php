<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureTemporaryFilesDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Provides a task command to ensure the temporary files directory.
 */
class EnsureTemporaryFilesDirectoryTask extends EnsureDirectoryTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:directory:ensure:tmp');
  }

  /**
   * {@inheritdoc}
   */
  function getPath() {
    return $this->getDrupalHelper()->getTemporaryFilesDirectoryPath();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure temporary files directory';
  }

}
