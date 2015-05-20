<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureSettingsFileTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

/**
 * Task command base class to ensure a settings file.
 */
abstract class EnsureSettingsFileTask extends EnsureFileTask {

  /**
   * Return path of directory containing settings files.
   *
   * @return string
   *   The path of the directory containing all settings files.
   */
  protected function getSiteSettingsDirectory() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings';
  }

}
