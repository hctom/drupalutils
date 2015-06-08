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

  /**
   * {@inheritdoc}
   */
  protected function skipWithMessage() {
    if (($message = parent::skipWithMessage())) {
      return $message;
    }

    $directories = array();

    // Has PHP been set with an upload_tmp_dir?
    if (ini_get('upload_tmp_dir')) {
      $directories [] = ini_get('upload_tmp_dir');
    }

    // Operating system specific dirs.
    if (substr(PHP_OS, 0, 3) == 'WIN') {
      $directories [] = 'c:\\windows\\temp';
      $directories [] = 'c:\\winnt\\temp';
    }
    else {
      $directories [] = '/tmp';
    }

    // PHP may be able to find an alternative tmp directory. This function
    // exists in PHP 5 >= 5.2.1, but Drupal requires PHP 5 >= 5.2.0, so we check
    // for it.
    if (function_exists('sys_get_temp_dir')) {
      $directories [] = sys_get_temp_dir();
    }

    // Operating system's default temporary files directory.
    foreach ($directories as $directory) {
      if ($directory === $this->getDrupalHelper()->getTemporaryFilesDirectoryPath()) {
        return "Using operating system's default temporary files directory";
      }
    }
  }

}
