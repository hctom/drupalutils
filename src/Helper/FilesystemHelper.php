<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\FilesystemHelper.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for the file system.
 */
class FilesystemHelper extends Helper {

  /**
   * Back up file or directory.
   *
   * @param string $path
   *   The path of the item to back up.
   *
   * @return string
   *   The path of the backed up item.
   *
   * @throws IOException
   */
  public function backup($path) {
    $suffix = 'backup.' . time();

    // Item is a file.
    if ($this->isFile($path)) {
      $info = pathinfo($path);

      if (empty($info['filename'])) {
        $target = dirname($path) . DIRECTORY_SEPARATOR . $info['basename'] . '.' . $suffix;
      }
      else {
        $target = dirname($path) . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $suffix . '.' . $info['extension'];
      }
    }

    // Item is a directory?
    elseif ($this->isDirectory($path) || $this->isSymlinkedDirectory($path)) {
      $target = $path . '.' . $suffix;
    }

    // Unknown item type.
    else {
      throw new IOException(sprintf('Unable to back up "%s"', $path), 0, NULL, $path);
    }

    // Back up item.
    $this->rename($path, $target);

    return $target;
  }

  /**
   * @see Filesystem::chgrp()
   */
  public function chgrp($files, $group, $recursive = FALSE) {
    $this->getFilesystem()->chgrp($files, $group, $recursive);
  }

  /**
   * @see Filesystem::chmod()
   */
  public function chmod($files, $mode, $umask = 0000, $recursive = FALSE) {
    $this->getFilesystem()->chmod($files, $mode, $umask, $recursive);
  }

  /**
   * @see Filesystem::dumpFile()
   */
  public function dumpFile($filename, $content) {
    $this->getFilesystem()->dumpFile($filename, $content);
  }

  /**
   * Item exists?
   *
   * @param string $path
   *   The path of the item to check.
   *
   * @return bool
   *   Whether the item exists or not.
   */
  public function exists($path) {
    return file_exists($path);
  }

  /**
   * Return Drupal helper.
   *
   * @return DrupalHelper
   *   The Drupal helper object.
   */
  protected function getDrupalHelper() {
    return $this->getHelperSet()->get('drupal');
  }

  /**
   * Return file system object.
   *
   * @return Filesystem
   *   The file system object.
   */
  protected function getFilesystem() {
    return new Filesystem();
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'filesystem';
  }

  /**
   * Return target of symbolic link.
   *
   * @param $path
   *   A symbolic link path.
   *
   * @return string
   *   The absolute symbolic link target path.
   *
   * @throws IOException
   */
  public function getSymlinkTarget($path) {
    $path = $this->makePathAbsolute($path);

    // Not a symbolic link.
    if (!$this->isSymlink($path)) {
      throw new IOException(sprintf('"%s" is not a symbolic link', $path), 0, NULL, $path);
    }

    $linkTarget = readlink($path);

    if (!$this->isAbsolutePath($linkTarget)) {
      $linkTarget = realpath(dirname($path) . DIRECTORY_SEPARATOR . $linkTarget);
    }

    return $linkTarget;
  }

  /**
   * @see Filesystem::isAbsolutePath()
   */
  public function isAbsolutePath($path) {
    return $this->getFilesystem()->isAbsolutePath($path);
  }

  /**
   * Item is a directory?
   *
   * @param string $path
   *   The path of the item to check.
   *
   * @return bool
   *   Whether the item is directory or not. This method returns FALSE if the
   *   item is a symbolic link to a directory.
   */
  public function isDirectory($path) {
    if (!$this->exists($path)) {
      return FALSE;
    }

    return is_dir($path) && !$this->isSymlink($path);
  }

  /**
   * Item is a file?
   *
   * @param string $path
   *   The path of the item to check.
   *
   * @return bool
   *   Whether the item is file or not. This method returns FALSE if the item is
   *   a symbolic link to a file.
   */
  public function isFile($path) {
    if (!$this->exists($path)) {
      return FALSE;
    }

    return is_file($path) && !$this->isSymlink($path);
  }

  /**
   * Item is a symbolic link?
   *
   * @param string $path
   *   The path of the item to check.
   *
   * @return bool
   *   Whether the item is a symbolic link.
   */
  public function isSymlink($path) {
    if (!$this->exists($path)) {
      return FALSE;
    }

    return is_link($path);
  }

  /**
   * Item is a symlinked directory?
   *
   * @param string $path
   *   The path of the item to check.
   *
   * @return bool
   *   Whether the item is a symlinked directory.
   */
  public function isSymlinkedDirectory($path) {
    return $this->isSymlink($path) && $this->isDirectory($this->getSymlinkTarget($path));
  }

  /**
   * Return absolute path.
   *
   * @param $path
   *   The path to rewrite (if necessary).
   *
   * @return string
   *   The absolute path.
   */
  public function makePathAbsolute($path) {
    if (!$this->getFilesystem()->isAbsolutePath($path)) {
      $path = $this->getDrupalHelper()->getRootDirectoryPath() . DIRECTORY_SEPARATOR . $path;
    }

    return $path;
  }

  /**
   * @see Filesystem::makePathRelative()
   */
  public function makePathRelative($endPath, $startPath = NULL) {
    $startPath = $startPath === NULL ? $this->getDrupalHelper()->getRootDirectoryPath() : $startPath;
    $endPath = $this->isAbsolutePath($endPath) ? $endPath : $this->makePathAbsolute($endPath);
    $endPathDirectory = pathinfo($endPath, PATHINFO_DIRNAME);
    $endPathFilename = pathinfo($endPath, PATHINFO_BASENAME);

    $relativeDirectoryPath = $this->getFilesystem()->makePathRelative($endPathDirectory, $startPath);

    return $relativeDirectoryPath . $endPathFilename;
  }

  /**
   * @see Filesystem::mkdir()
   */
  public function mkdir($dirs, $mode = 0777) {
    $this->getFilesystem()->mkdir($dirs, $mode);
  }

  /**
   * Read a file's contents.
   *
   * @param string $path
   *   The path of the file to read.
   *
   * @return string
   *   The file's contents.
   */
  public function readFile($path) {
    // File does not exist?
    if (!$this->exists($path)) {
      throw new RuntimeException(sprintf('File "%s" does not exist', $path));
    }

    // Is not a file?
    if (!$this->isFile($path)) {
      throw new RuntimeException(sprint('"%s" is not a file', $path));
    }

    return file_get_contents($path);
  }

  /**
   * @see Filesystem::remove()
   */
  public function remove($paths) {
    $this->getFilesystem()->remove($paths);
  }

  /**
   * @see Filesystem::rename()
   */
  public function rename($origin, $target, $overwrite = FALSE) {
    $this->getFilesystem()->rename($origin, $target, $overwrite);
  }

  /**
   * @see Filesystem::symlink()
   */
  public function symlink($originDir, $targetDir, $copyOnWindows = FALSE) {
    $this->getFilesystem()->symlink($originDir, $targetDir, $copyOnWindows);
  }

}
