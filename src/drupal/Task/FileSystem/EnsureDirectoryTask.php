<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureDirectoryTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Drupal utilities task base class: Ensure directory.
 */
abstract class EnsureDirectoryTask extends FilesystemTask {

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $path = $this->getPath();
    $mode = $this->getMode();

    // Files directory exists?
    if ($this->getFilesystemHelper()->exists($path)) {
      // Is a directory?
      if (!is_dir($path)) {
        throw new IOException(sprintf('"%s" is not a directory', $path), 0, NULL, $path);
      }

      $output->writeln(sprintf('"<comment>%s</comment>" already exists', $path));
    }
    else {
      $this->getFilesystemHelper()->mkdir($path, $mode);
    }

    // Ensure permissions.
    $this->getFilesystemHelper()->chmod($path, $mode);
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
