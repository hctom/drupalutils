<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\FileSystem\EnsureDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\FileSystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Drupal utilities task base class: Ensure directory.
 */
abstract class EnsureDirectoryTask extends FileSystemTask {

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $path = $this->getPath();
    $mode = $this->getMode();

    // Files directory exists?
    if ($this->fileSystem()->exists($path)) {
      // Is a directory?
      if (!is_dir($path)) {
        throw new IOException(sprintf('"%s" is not a directory', $path), 0, NULL, $path);
      }

      $output->writeln(sprintf('"<comment>%s</comment>" already exists', $path));
    }
    else {
      $this->fileSystem()->mkdir($path, $mode);
    }

    // Ensure permissions.
    $this->fileSystem()->chmod($path, $mode);
  }

  /**
   * Return path.
   *
   * @return string
   *   The path of the directory to ensure.
   */
  abstract public function getPath();

  /**
   * Return file mode.
   *
   * @return int
   *   The file mode to apply to the directory to ensure.
   */
  public function getMode() {
    return 0777;
  }

}
