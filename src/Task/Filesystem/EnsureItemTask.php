<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureItemTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Task command base class to ensure a file system item.
 */
abstract class EnsureItemTask extends FilesystemTask {

  /**
   * Ensure file mode.
   */
  protected function ensureFileMode() {
    $filesystem = $this->getFilesystemHelper();
    $path = $this->getPath();
    $fileMode = $this->getFileMode();
    $fileModeOctal = 0 . decoct($fileMode);
    $originalFileModeOctal = substr(sprintf('%o', fileperms($path)), -4);
    $recursive = $filesystem->isFile($path) || $filesystem->isSymlinkedFile($path) ? FALSE : $this->getApplyFileModeRecursively();

    // File permissions are already set to the correct value?
    if ($originalFileModeOctal === $fileModeOctal && !$recursive) {
      $this->getLogger()->notice('<label>Permissions already set:</label> {path} ==> {mode}', array(
        'mode' => '<comment>' . $fileModeOctal . '</comment>',
        'path' => $this->getFormatterHelper()->formatPath($path),
      ));
    }

    // Ensure permissions.
    else {
      $this->getFilesystemHelper()->chmod($path, $fileMode, 0000, $recursive);

      if ($recursive) {
        $this->getLogger()->notice('<label>Recursively set permissions:</label> {path} ==> {mode}', array(
          'mode' => '<comment>' . $fileModeOctal . '</comment>',
          'path' => $this->getFormatterHelper()->formatPath($path),
        ));
      }
      else {
        $this->getLogger()->notice('<label>Set permissions:</label> {path} ==> {mode}', array(
          'mode' => '<comment>' . $fileModeOctal . '</comment>',
          'path' => $this->getFormatterHelper()->formatPath($path),
        ));
      }
    }
  }

  /**
   * Ensure group.
   */
  protected function ensureGroup() {
    if ($this->getGroup() !== NULL) {
      $filesystem = $this->getFilesystemHelper();
      $path = $this->getPath();
      $recursive = $filesystem->isFile($path) || $filesystem->isSymlinkedDirectory($path) ? FALSE : $this->getApplyGroupRecursively();
      $group = $this->getGroup();
      $group = is_numeric($group) ? posix_getgrgid($group) : posix_getgrnam($group);
      $originalGroup = posix_getgrgid(filegroup($path));

      // Group is already set to the correct value?
      if ($group['name'] == $originalGroup['name'] && !$recursive) {
        $this->getLogger()->notice('<label>Group already set:</label> {path} ==> {group}', array(
          'group' => '<comment>' . $group['name'] . '</comment>',
          'path' => $this->getFormatterHelper()->formatPath($path),
        ));
      }

      // Ensure group.
      else {
        $filesystem->chgrp($path, $group['name'], $recursive);

        if ($recursive) {
          $this->getLogger()->notice('<label>Recursively set group:</label> {group} ==> {path}', array(
            'group' => '<comment>' . $group['name'] . '</comment>',
            'path' => $this->getFormatterHelper()->formatPath($path),
          ));
        }
        else {
          $this->getLogger()->notice('<label>Set group:</label> {path} ==> {group}', array(
            'group' => '<comment>' . $group['name'] . '</comment>',
            'path' => $this->getFormatterHelper()->formatPath($path),
          ));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $path = $this->getPath();

    // Item does not exist?
    if (!$this->getFilesystemHelper()->exists($path)) {
      throw new IOException(sprintf('"%s" does not exist', $path), 0, NULL, $path);
    }

    // Ensure file mode.
    $this->ensureFileMode();

    // Ensure group.
    $this->ensureGroup();
  }

  /**
   * Apply file mode recursively?
   *
   * @return int
   *   Whether to apply the file mode recursively.
   */
  public function getApplyFileModeRecursively() {
    return FALSE;
  }

  /**
   * Apply group recursively?
   *
   * @return int
   *   Whether to apply the group recursively.
   */
  public function getApplyGroupRecursively() {
    return FALSE;
  }

  /**
   * Return file mode.
   *
   * @return int
   *   The file mode to apply to the item to ensure.
   */
  public function getFileMode() {
    return 0777;
  }

  /**
   * Return group.
   *
   * @return string|int|null
   *   The group to apply to the item to ensure. Return NULL to leave as is.
   */
  public function getGroup() {
    return NULL;
  }

  /**
   * Return path.
   *
   * @return string
   *   The path of the item to ensure.
   */
  abstract public function getPath();

}
