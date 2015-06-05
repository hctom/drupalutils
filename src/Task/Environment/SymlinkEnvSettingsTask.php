<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Environment\SymlinkEnvSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Environment;

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
    parent::configure();

    $this
      ->setName('task:environment:settings:symlink');
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
    return 'Symlink environment specific settings.php file';
  }

}
