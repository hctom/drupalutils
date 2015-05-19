<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureFileTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Task command base class to ensure a file.
 */
abstract class EnsureFileTask extends FilesystemTask {

  /**
   * Mode: Skip if exists.
   */
  const MODE_REBUILD_IF_EXISTS = 'rebuild-if-exists';

  /**
   * Mode: Rebuild if exists.
   */
  const MODE_SKIP_IF_EXISTS = 'skip-if-exists';

  /**
   * @param InputInterface $input
   *   The input.
   * @param OutputInterface $output
   *   The output.
   *
   * @return array
   *   The file contents.
   */
  public function buildContent(InputInterface $input, OutputInterface $output) {
    return array();
  }

  /**
   * Dump content into a file.
   *
   * @param string $filename
   *   The file to be written to.
   * @param array|string $content
   *   The data to write into the file.
   * @param int $fileMode
   *   The file mode (octal) to apply.
   */
  protected function dumpFile($filename, $content, $fileMode) {
    // Prepare content.
    $content = implode("\n\n", is_array($content) ? $content : array($content)) . "\n";

    // Dump file.
    $this->getFilesystemHelper()->dumpFile($filename, $content);

    // Ensure file mode.
    $this->getFilesystemHelper()->chmod($filename, $fileMode);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filename = $this->getFilesystemHelper()->makePathAbsolute($this->getPath());
    $mode = $this->getMode();

    // File does not exist.
    if (!file_exists($filename)) {
      $this->dumpFile($filename, $this->buildContent($input, $output), $this->getFileMode());

      $this->getLogger()->always('<success>Created {filename} file</success>', array(
        'filename' => $this->getFormatterHelper()->formatPath($filename),
      ));
    }

    // Existing item is a file?
    elseif (!is_file($filename)) {
      throw new RuntimeException(sprint('"%s" is not a file', $filename));
    }

    // Skip if exists?
    elseif ($mode === static::MODE_SKIP_IF_EXISTS) {
      $this->getLogger()->always('<success>File {filename} already exists</success>', array(
        'filename' => $this->getFormatterHelper()->formatPath($filename),
      ));
    }

    // Rebuild if exists?
    elseif ($mode === static::MODE_REBUILD_IF_EXISTS) {
      $this->dumpFile($filename, $this->buildContent($input, $output), $this->getFileMode());

      $this->getLogger()->always('<success>Rebuilt {filename} file</success>', array(
        'filename' => $this->getFormatterHelper()->formatPath($filename),
      ));
    }
  }

  /**
   * Return file mode.
   *
   * @return int
   *   The file mode to apply to the file to ensure.
   */
  public function getFileMode() {
    return 0644;
  }

  /**
   * Return ensure file mode.
   *
   * @return string
   *   The ensure file mode. Possible values:
   *     - static::MODE_REBUILD_IF_EXISTS: Rebuild existing file.
   *     - static::MODE_SKIP_IF_EXISTS: Skip existing item (default).
   */
  public function getMode() {
    return static::MODE_SKIP_IF_EXISTS;
  }

  /**
   * Return path.
   *
   * @return string
   *   The path of the file to ensure.
   */
  abstract public function getPath();

}
