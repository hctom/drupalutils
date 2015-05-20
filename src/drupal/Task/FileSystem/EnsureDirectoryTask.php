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
 * Task command base class to ensure a directory.
 */
abstract class EnsureDirectoryTask extends EnsureItemTask {

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filesystem = $this->getFilesystemHelper();
    $path = $this->getPath();

    // Directory not found -> create directory.
    if (!$filesystem->exists($path)) {
      $filesystem->mkdir($path, $this->getFileMode());

      $this->getLogger()->notice('<label>Created directory:</label> {path}', array(
        'path' => $this->getFormatterHelper()->formatPath($path),
      ));
    }

    // Existing item is not a directory.
    elseif (!$filesystem->isDirectory($path)) {
      throw new IOException(sprintf('"%s" is not a directory', $path), 0, NULL, $path);
    }

    // Directory already exists.
    else {
      $this->getLogger()->notice('Directory {path} already exists', array(
        'path' => $this->getFormatterHelper()->formatPath($path),
      ));
    }

    // Call parent to ensure permissions/group.
    $exitCode = parent::execute($input, $output);

    $this->getLogger()->always('<success>Ensured {path} directory</success>', array(
      'path' => $this->getFormatterHelper()->formatPath($path),
    ));

    return $exitCode;
  }

}
