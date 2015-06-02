<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrupalHelper.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for Drupal.
 */
class DrupalHelper extends Helper {

  /**
   * Return name of administrative theme.
   *
   * @return string
   *   The name of the administrative theme.
   *
   * @throws RuntimeException
   */
  public function getAdminTheme() {
    $themeName = NULL;
    $status = $this->getCoreStatus();

    // Contains default theme.
    if (property_exists($status, 'admin-theme')) {
      $themeName = $status->{'admin-theme'};
    }

    if (!$themeName) {
      throw new RuntimeException('Unable to determine administrative theme');
    }

    return $themeName;
  }

  /**
   * Return Drupal core status information.
   *
   * @return \stdClass
   *   An object containing Drupal core status information.
   *
   * @throws RuntimeException
   */
  protected function getCoreStatus() {
    $process = $this->getDrushProcessHelper()
      ->setCommandName('core-status')
      ->setOptions(array(
        'pipe' => TRUE,
      ))
      ->mustRun(NULL, "Unable to determine Drupal's core status", FALSE);

    // Error parsing core status.
    if (($status = json_decode($process->getOutput())) === NULL) {
      throw new RuntimeException("Unable to parse Drupal's core status");
    }

    return $status;
  }

  /**
   * Return Drush process helper.
   *
   * @return DrushProcessHelper
   *   The reset Drush process helper object.
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
   * Return name of default theme.
   *
   * @return string
   *   The name of the default theme.
   *
   * @throws RuntimeException
   */
  public function getDefaultTheme() {
    $themeName = NULL;
    $status = $this->getCoreStatus();

    // Contains default theme.
    if (property_exists($status, 'theme')) {
      $themeName = $status->theme;
    }

    if (!$themeName) {
      throw new RuntimeException('Unable to determine default theme');
    }

    return $themeName;
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
   * @throws RuntimeException
   */
  public function getPublicFilesDirectoryPath() {
    static $path;

    if (!isset($path)) {
      $path = NULL;
      $status = $this->getCoreStatus();

      // Contains public files directory path.
      if (property_exists($status, 'files')) {
        $path = $status->files;
      }
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
   *
   * @throws IOException
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
   */
  public function getSiteDirectoryPath() {
    static $path;

    $filesystem = $this->getFilesystemHelper();

    if (!isset($path)) {
      $path = NULL;
      $status = $this->getCoreStatus();

      // Contains site directory path.
      if (property_exists($status, 'site')) {
        $path = $status->site;
      }

      // Fall back to 'sites/[HOSTNAME-FROM-URI]' (if exists).
      if (!$path) {
        if (($drushSiteAliasConfig = $this->getDrushSiteAliasHelper()->getConfig())) {
          $path = 'sites' . DIRECTORY_SEPARATOR . $drushSiteAliasConfig->getHostName();
          if (!$filesystem->isDirectory($filesystem->makePathAbsolute($path))) {
            $path = NULL;
          }
        }
      }

      // Fall back to 'sites/default'.
      if (!$path) {
        $path = 'sites' . DIRECTORY_SEPARATOR . 'default';
      }
    }

    return $filesystem->makePathAbsolute($path);
  }

  /**
   * Return Drupal's temporary files directory path.
   *
   * @return string
   *   The absolute path of the temporary files directory.
   *
   * @throws RuntimeException
   */
  public function getTemporaryFilesDirectoryPath() {
    static $path;

    if (!isset($path)) {
      $path = NULL;
      $status = $this->getCoreStatus();

      // Contains temporary files directory path.
      if (property_exists($status, 'temp')) {
        $path = $status->temp;
      }
    }

    if (!$path) {
      throw new RuntimeException("Unable to determine Drupal's temporary files directory path");
    }

    return $this->getFilesystemHelper()->makePathAbsolute($path);
  }

  /**
   * Return Drupal version.
   *
   * @return string
   *   The Drupal version.
   *
   * @throws RuntimeException
   */
  public function getVersion() {
    static $version;

    if (!isset($version)) {
      $version = NULL;
      $status = $this->getCoreStatus();

      // Contains version.
      if (property_exists($status, 'drupal-version')) {
        $version = $status->{'drupal-version'};
      }
    }

    if (!$version) {
      throw new RuntimeException('Unable to determine Drupal version');
    }

    return $version;
  }

}
