<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Environment\SymLinkEnvHtaccessTask.
 */

namespace hctom\DrupalUtils\Task\Environment;

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
      ->setName('task:environment:htaccess:symlink');
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
    return 'Symlink environment specific .htaccess file';
  }

}
