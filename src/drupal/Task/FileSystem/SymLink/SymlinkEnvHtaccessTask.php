<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\SymLink\SymLinkEnvHtaccessTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem\SymLink;

use hctom\DrupalUtils\Task\Filesystem\SymLinkTask;

/**
 * Provides a task command to create a symbolic link to the environment specific
 * .htaccess file.
 */
class SymlinkEnvHtaccessTask extends SymLinkTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:symlink:htaccess.env');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return '.htaccess';
  }

  /**
   * {@inheritdoc}
   */
  public function getTarget() {
    return '.htaccess.' . $this->getDrupalHelper()->getEnvironment();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Symbolic link to environment specific .htaccess file';
  }

}
