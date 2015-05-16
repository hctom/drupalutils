<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\Symlink\SymlinkEnvSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem\Symlink;

use hctom\DrupalUtils\Task\Filesystem\SymlinkTask;

/**
 * Provides a task command to create a symbolic link to the environment specific
 * settings.php file.
 */
class SymlinkEnvSettingsTask extends SymlinkTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:symlink:settings.env');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings.php';
  }

  /**
   * {@inheritdoc}
   */
  public function getTarget() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings.' . $this->getDrupalHelper()->getEnvironment() . '.php';
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Symbolic link to environment specific settings.php file';
  }

}
