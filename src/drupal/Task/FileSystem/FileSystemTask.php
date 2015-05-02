<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\FileSystem\FileSystemTask.
 */

namespace hctom\DrupalUtils\Task\FileSystem;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Drupal utilities task base class: File system operation.
 */
abstract class FileSystemTask extends Task {

  /**
   * Return file system helper.
   *
   * @return Filesystem
   *   The file system helper object.
   */
  protected function fileSystem() {
    return new Filesystem();
  }

}
