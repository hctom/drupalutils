<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\FilesystemHelper.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for the file system.
 */
class FilesystemHelper extends Helper {

  /**
   * @see Filesystem::dumpFile()
   */
  public function dumpFile($filename, $content, $mode = 0666) {
    return $this->getFilesystem()->dumpFile($filename, $content, $mode);
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
   * @see Filesystem::isAbsolutePath()
   */
  public function isAbsolutePath($path) {
    return $this->getFilesystem()->isAbsolutePath($path);
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
    $endPathDirectory = pathinfo($endPath, PATHINFO_DIRNAME);
    $endPathFilename = pathinfo($endPath, PATHINFO_BASENAME);

    $relativeDirectoryPath = $this->getFilesystem()->makePathRelative($endPathDirectory, $startPath);

    return $relativeDirectoryPath . $endPathFilename;
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
    if (!file_exists($path)) {
      throw new RuntimeException(sprintf('File "%s" does not exist', $path));
    }

    // Is not a file?
    if (!is_file($path)) {
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
