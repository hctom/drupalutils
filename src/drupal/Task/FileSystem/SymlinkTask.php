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
    $link = $this->getAbsoluteLink();
    $target = $this->getAbsoluteTarget();

    // Symbolic link target does not exist?
    if (!file_exists($target)) {
      throw new IOException(sprintf('Symbolic link target "%s" does not exist', $target), 0, NULL, $target);
    }

    // Symlink already exists?
    elseif (is_link($link) && readlink($link) === $target) {
      $this->getLogger()->always('<success>Symbolic link already exists: {link} ==> {target}</success>', array(
        'link' => $this->getFormatterHelper()->formatPath($link),
        'target' => $this->getFormatterHelper()->formatPath($target),
      ));

      return;
    }

    // Symbolic link already exists as physical file/directory?
    elseif (file_exists($link)) {
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

    // Throw error if item already exists?
    if ($mode === static::MODE_ERROR_IF_EXISTS) {
      if (is_link($link)) {
        throw new IOException(sprintf('Symbolic link "%s" already exists as symbolic link with different target "%s"', $link, readlink($link)), 0, NULL, $link);
      }
      elseif (is_file($link)) {
        throw new IOException(sprintf('Symbolic link "%s" already exists as file', $link), 0, NULL, $link);
      }
      elseif (is_dir($link)) {
        throw new IOException(sprintf('Symbolic link "%s" already exists as directory', $link), 0, NULL, $link);
      }
    }

    // Display information about existing item.
    if (is_link($link)) {
      $this->getLogger()->notice('Symbolic link {link} already exists as symbolic link with different target {target}', array(
        'link' => $this->getFormatterHelper()->formatPath($link),
        'target' => $this->getFormatterHelper()->formatPath($this->getFilesystemHelper()->makePathAbsolute(readlink($link))),
      ));
    }
    elseif (is_file($link)) {
      $this->getLogger()->notice('Symbolic link {link} already exists as file', array(
        'link' => $this->getFormatterHelper()->formatPath($link),
      ));
    }
    elseif (is_dir($link)) {
      $this->getLogger()->notice('Symbolic link {link} already exists as directory', array(
        'link' => $this->getFormatterHelper()->formatPath($link),
      ));
    }

    // Process existing link depending on mode.
    switch ($mode) {
      case static::MODE_BACKUP_IF_EXISTS:
        // Backup exsiting item.
        $backup = $this->getFilesystemHelper()->backup($link);

        $this->getLogger()->notice('Created a backup of {original} ==> {backup}', array(
          'original' => $this->getFormatterHelper()->formatPath($link),
          'backup' => $this->getFormatterHelper()->formatPath($backup),
        ));
        break;

      case static::MODE_DELETE_IF_EXISTS:
        $this->remove($link);
        break;
    }
  }

  /**
   * Remove file or directory.
   *
   * @param string $path
   *   The path of the item to remove.
   */
  protected function remove($path) {
    // DElete existing item.
    $this->getFilesystemHelper()->remove($path);

    $this->getLogger()->notice('Removed {path} to allow a symbolic link at that location', array(
      'path' => $this->getFormatterHelper()->formatPath($path),
    ));
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
    $this->getFilesystemHelper()->symlink($target, $link);

    $this->getLogger()->always('<success>Created symbolic link: {link} ==> {target}</success>', array(
      'link' => $this->getFormatterHelper()->formatPath($link),
      'target' => $this->getFormatterHelper()->formatPath($target),
    ));
  }

}
