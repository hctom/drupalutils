<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrupalHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Output\OutputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for Drupal.
 */
class DrupalHelper extends Helper  implements LoggerAwareInterface, OutputAwareInterface {

  use LoggerAwareTrait;
  use OutputAwareTrait;

  /**
   * Return Drush process helper.
   *
   * @return DrushProcessHelper
   *   The resetted Drush process helper object.
   */
  protected function getDrushProcessHelper() {
    return $this->getHelperSet()->get('drush_process')->reset();
  }

  /**
   * Return Drush helper.
   *
   * @return DrushSiteAliasHelper
   *   The Drush helper object.
   */
  protected function getDrushSiteAliasHelper() {
    return $this->getHelperSet()->get('drush_site_alias');
  }

  /**
   * Return environment indicator.
   *
   * @return string
   *   The environment indicator.
   */
  public function getEnvironment() {
    return $this->getDrushSiteAliasHelper()->getConfig()->getEnvironment();
  }

  /**
   * Return file system helper.
   *
   * @return FilesystemHelper
   *   The file system helper object.
   */
  protected function getFilesystemHelper() {
    return $this->getHelperSet()->get('filesystem');
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drupal';
  }

  /**
   * Return Drupal's public files directory path.
   *
   * @return string
   *   The absolute path of the public files directory.
   *
   * @throws \RuntimeException
   */
  public function getPublicFilesDirectoryPath() {
    static $path;

    if (!isset($path)) {
      $process = $this->getDrushProcessHelper()
        ->setCommandName('core-status')
        ->setOptions(array(
          'fields' => 'files',
          'field-labels' => '0',
        ))
        ->run(NULL, "Unable to determine Drupal's public files directory path", FALSE);
      $path = trim($process->getOutput());
    }

    if (!$path) {
      throw new RuntimeException("Unable to determine Drupal's public files directory path");
    }

    return $this->getFilesystemHelper()->makePathAbsolute($path);
  }

  /**
   * Return Drupal's root path.
   *
   * @return string
   *   The absolute path of Drupal's document root.
   */
  public function getRootDirectoryPath() {
    /* @var FilesystemHelper $filesystem */
    $filesystem = $this->getHelperSet()->get('filesystem');

    $path = $this->getDrushSiteAliasHelper()->getConfig()->getRootPath();

    // Root directory path is not absolute?
    if (!$filesystem->isAbsolutePath($path)) {
      throw new IOException(sprintf("No absolute path provided for Drupal's document root"));
    }

    return $path;
  }

  /**
   * Return Drupal's site directory path.
   *
   * @return string
   *   The absolute path of the site directory.
   *
   * @throws \RuntimeException
   */
  public function getSiteDirectoryPath() {
    static $path;

    if (!isset($path)) {
      $process = $this->getDrushProcessHelper()
        ->setCommandName('core-status')
        ->setOptions(array(
          'fields' => 'site',
          'field-labels' => '0',
        ))
        ->run(NULL, "Unable to determine Drupal's site directory path", FALSE);

      $path = trim($process->getOutput());
    }

    if (!$path) {
      throw new \RuntimeException("Unable to determine Drupal's site directory path");
    }

    return $this->getFilesystemHelper()->makePathAbsolute($path);
  }

  /**
   * Return Drupal's temporary files directory path.
   *
   * @return string
   *   The absolute path of the temporary files directory.
   *
   * @throws \RuntimeException
   */
  public function getTemporaryFilesDirectoryPath() {
    static $path;

    if (!isset($path)) {
      $process = $this->getDrushProcessHelper()
        ->setCommandName('core-status')
        ->setOptions(array(
          'fields' => 'temp',
          'field-labels' => '0',
        ))
        ->run(NULL, "Unable to determine Drupal's temporary files directory path", FALSE);

      $path = trim($process->getOutput());
    }

    if (!$path) {
      throw new RuntimeException("Unable to determine Drupal's temporary files directory path");
    }

    return $this->getFilesystemHelper()->makePathAbsolute($$path);
  }

  /**
   * Return Drupal version.
   *
   * @return string
   *   The Drupal version.
   *
   * @throws \RuntimeException
   */
  public function getVersion() {
    static $version;

    if (!isset($version)) {
      $process = $this->getDrushProcessHelper()
        ->setCommandName('core-status')
        ->setOptions(array(
          'fields' => 'drupal-version',
          'field-labels' => '0',
        ))
        ->run(NULL, 'Unable to determine Drupal version', FALSE);

      $version = trim($process->getOutput());
    }

    if (!$version) {
      throw new RuntimeException('Unable to determine Drupal version');
    }

    return $version;
  }

}
