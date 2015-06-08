<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\SymLinkTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Task command base class to create a symbolic link.
 */
abstract class SymlinkTask extends FilesystemTask {

  /**
   * Mode: Backup item if link already exists.
   */
  const MODE_BACKUP_IF_EXISTS = 'backup-if-exists';

  /**
   * Mode: Delete item if link already exists.
   */
  const MODE_DELETE_IF_EXISTS = 'delete-if-exists';

  /**
   * Mode: Throw error if link already exists.
   */
  const MODE_ERROR_IF_EXISTS = 'error-if-exists';

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filesystem = $this->getFilesystemHelper();
    $formatter = $this->getFormatterHelper();
    $link = $this->getAbsoluteLink();
    $target = $this->getAbsoluteTarget();

    // Symbolic link target does not exist?
    if (!$filesystem->exists($target)) {
      throw new IOException(sprintf('Symbolic link target "%s" does not exist', $target), 0, NULL, $target);
    }

    // Symlink already exists?
    elseif ($filesystem->isSymlink($link) && $filesystem->getSymlinkTarget($link) === $target) {
      $this->getLogger()->always('<success>Symbolic link {link} ==> {target} already exists</success>', array(
        'link' => $formatter->formatPath($link),
        'target' => $formatter->formatPath($target),
      ));

      return;
    }

    // Symbolic link already exists as physical file/directory?
    elseif ($filesystem->exists($link)) {
      $this->ifExists($link);
    }

    // Create symbolic link.
    return $this->symlink($link, $target);
  }

  /**
   * Return absolute link path.
   *
   * @return string
   *   The absolute path of the symbolic link.
   */
  public function getAbsoluteLink() {
    if (!$this->getLink()) {
      throw new \RuntimeException('No symbolic link name specified');
    }

    return $this->getFilesystemHelper()->makePathAbsolute($this->getLink());
  }

  /**
   * Return absolute target path.
   *
   * @return string
   *   The absolute path where the symbolic link should point to.
   */
  public function getAbsoluteTarget() {
    if (!$this->getTarget()) {
      throw new \RuntimeException('No symbolic link target specified');
    }

    return $this->getFilesystemHelper()->makePathAbsolute($this->getTarget());
  }

  /**
   * Return link path.
   *
   * @return string
   *   The path of the symbolic link.
   */
  abstract public function getLink();

  /**
   * Return symbolic link mode.
   *
   * @return string
   *   The symbolic link mode. Possible values:
   *     - static::MODE_BACKUP_IF_EXISTS: Backup any existing item (default).
   *     - static::MODE_DELETE_IF_EXISTS: Delete any existing item.
   *     - static::MODE_ERROR_IF_EXISTS: Throw an error for any existing item.
   */
  public function getMode() {
    return static::MODE_BACKUP_IF_EXISTS;
  }

  /**
   * Return target path.
   *
   * @return string
   *   The path the symbolic link should point to.
   */
  abstract public function getTarget();

  /**
   * Process existing physical item.
   *
   * @param $link
   *   The path of the symbolic link to create.
   */
  protected function ifExists($link) {
    $mode = $this->getMode();
    $filesystem = $this->getFilesystemHelper();
    $formatter = $this->getFormatterHelper();

    // Throw error if item already exists?
    if ($mode === static::MODE_ERROR_IF_EXISTS) {
      if ($filesystem->isSymlink($link)) {
        throw new IOException(sprintf('Symbolic link "%s" already exists as symbolic link with different target "%s"', $link, $filesystem->getSymlinkTarget($link)), 0, NULL, $link);
      }
      elseif ($filesystem->isFile($link)) {
        throw new IOException(sprintf('Symbolic link "%s" already exists as file', $link), 0, NULL, $link);
      }
      elseif ($filesystem->isDirectory($link)) {
        throw new IOException(sprintf('Symbolic link "%s" already exists as directory', $link), 0, NULL, $link);
      }
    }

    // Display information about existing item.
    if ($filesystem->isSymlink($link)) {
      $this->getLogger()->notice('Symbolic link {link} already exists as symbolic link with different target {target}', array(
        'link' => $formatter->formatPath($link),
        'target' => $formatter->formatPath($filesystem->getSymlinkTarget($link)),
      ));
    }
    elseif ($filesystem->isFile($link)) {
      $this->getLogger()->notice('Symbolic link {link} already exists as file', array(
        'link' => $formatter->formatPath($link),
      ));
    }
    elseif ($filesystem->isDirectory($link)) {
      $this->getLogger()->notice('Symbolic link {link} already exists as directory', array(
        'link' => $formatter->formatPath($link),
      ));
    }

    // Process existing link depending on mode.
    switch ($mode) {
      case static::MODE_BACKUP_IF_EXISTS:
        // Backup exsiting item.
        $backup = $filesystem->backup($link);

        $this->getLogger()->notice('Created backup of {original} ==> {backup} to allow a symbolic link at that location', array(
          'original' => $formatter->formatPath($link),
          'backup' => $formatter->formatPath($backup),
        ));
        break;

      case static::MODE_DELETE_IF_EXISTS:
        $filesystem->remove($link);

        $this->getLogger()->notice('Removed {path} to allow a symbolic link at that location', array(
          'path' => $formatter->formatPath($link),
        ));
        break;
    }
  }

  /**
   * Create a symbolic link.
   *
   * @param string $link
   *   The absolute path of the symbolic link.
   * @param string $target.
   *   The absolute path the symbolic link should point to.
   */
  protected function symlink($link, $target) {
    $formatter = $this->getFormatterHelper();

    $this->getFilesystemHelper()->symlink($target, $link);

    $this->getLogger()->always('<success>Created {link} ==> {target} symbolic link</success>', array(
      'link' => $formatter->formatPath($link),
      'target' => $formatter->formatPath($target),
    ));
  }

}
