<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsurePrivateFilesDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Provides a task command to ensure the private files directory.
 */
class EnsurePrivateFilesDirectoryTask extends EnsureDirectoryTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:directory:ensure:private');
  }

  /**
   * {@inheritdoc}
   */
  function getPath() {
    return $this->getDrupalHelper()->getPrivateFilesDirectoryPath();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure private files directory';
  }

  /**
   * {@inheritdoc}
   */
  protected function skipWithMessage() {
    if (($message = parent::skipWithMessage())) {
      return $message;
    }

    // No private files directory path set.
    try {
      $this->getDrupalHelper()->getPrivateFilesDirectoryPath();
    }
    catch(\Exception $e) {
      return 'No private files directory specified';
    }
  }


}
