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
abstract class EnsureFileTask extends EnsureItemTask {

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

  // TODO Method really needed when template engine has been implemented?
  /**
   * Dump content into a file.
   *
   * @param string $filename
   *   The file to be written to.
   * @param array|string $content
   *   The data to write into the file.
   */
  protected function dumpFile($filename, $content) {
    // Prepare content.
    $content = implode("\n\n", is_array($content) ? $content : array($content)) . "\n";

    // Dump file.
    $this->getFilesystemHelper()->dumpFile($filename, $content, $this->getFileMode());
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filename = $this->getFilesystemHelper()->makePathAbsolute($this->getPath());
    $filesystem = $this->getFilesystemHelper();
    $formatter = $this->getFormatterHelper();

    // File does not exist -> create file.
    if (!$filesystem->exists($filename)) {
      $this->dumpFile($filename, $this->buildContent($input, $output));

      $this->getLogger()->notice('<label>Created file:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Existing item is not a file.
    elseif (!$filesystem->isFile($filename)) {
      throw new RuntimeException(sprint('"%s" is not a file', $filename));
    }

    // Skip if exists.
    elseif ($this->getSkipIfExists()) {
      $this->getLogger()->notice('<label>File already exists:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Rebuild file.
    else {
      $this->dumpFile($filename, $this->buildContent($input, $output));

      $this->getLogger()->notice('<label>Rebuilt file:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Call parent to ensure permissions/group.
    $exitCode = parent::execute($input, $output);

    $this->getLogger()->always('<success>Ensured {path} file</success>', array(
      'path' => $this->getFormatterHelper()->formatPath($filename),
    ));

    return $exitCode;
  }

  /**
   * {@inheritdoc}
   */
  public function getFileMode() {
    return 0644;
  }

  /**
   * Skip if exists?
   *
   * @return bool
   *   Whether to skip the file if it already exists. Return FALSE to rebuild
   *   the existing file.
   */
  public function getSkipIfExists() {
    return TRUE;
  }

}
