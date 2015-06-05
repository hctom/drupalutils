<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureSiteDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Provides a task command to ensure the site directory.
 */
class EnsureSiteDirectoryTask extends EnsureDirectoryTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:directory:ensure:site');
  }

  /**
   * {@inheritdoc}
   */
  function getPath() {
    return $this->getDrupalHelper()->getSiteDirectoryPath();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure site directory';
  }

}
