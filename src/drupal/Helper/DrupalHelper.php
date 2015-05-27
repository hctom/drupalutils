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
class DrupalHelper extends Helper implements LoggerAwareInterface, OutputAwareInterface {

  use LoggerAwareTrait;
  use OutputAwareTrait;

  /**
   * Return Drupal core status information.
   *
   * @return \stdClass
   *   An object containing Drupal core status information.
   *
   * @throws RuntimeException
   */
  protected function getCoreStatus() {
    static $status;

    if (!isset($status)) {
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
    }

    return $status;
  }

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
   * Return name of default theme.
   *
   * @return string
   *   The name of the default theme.
   */
  public function getDefaultTheme() {
    return $this->getVariable('theme_default');
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
   * @throws \RuntimeException
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
   * Return variable value.
   *
   * @param $variableName
   *   The name of the variable to get the value of.
   *
   * @return mixed
   *   The variable value.
   */
  public function getVariable($variableName) {
    static $cache;

    if (!isset($cache[$variableName])) {
      $cache[$variableName] = FALSE;

      // Prepare error message.
      $errorMessage = array(
        'Unable to determine value of {name} variable',
        array(
          'name' => $variableName,
        ),
      );

      // Determine variable value via Drush.
      $process = $this->getDrushProcessHelper()
        ->setCommandName('variable-get')
        ->setArguments(array(
          'name' => $variableName,
        ))
        ->setOptions(array(
          'exact' => TRUE,
          'format' => 'json',
          'pipe' => TRUE,
        ))
        ->mustRun(NULL, $errorMessage, FALSE);

      // Unable to parse variable data.
      if (($data = json_decode($process->getOutput())) === NULL) {
        throw new RuntimeException(sprintf('Unable to parse variable value of "%s"', $variableName));
      }

      // Variable does not exist.
      if (!property_exists($data, $variableName)) {
        throw new RuntimeException(sprintf('Unable to determine value of "%s" variable', $variableName));
      }

      $cache[$variableName] = $data->$variableName;
    }

    return $cache[$variableName];
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

  /**
   * Module exists?
   *
   * @param $moduleName
   *   The name of the module.
   *
   * @return bool
   *   TRUE if the module is both installed and enabled, FALSE otherwise.
   */
  public function moduleExists($moduleName) {
    static $cache = array();

    if (!isset($cache[$moduleName])) {
      $cache[$moduleName] = FALSE;

      $process = $this->getDrushProcessHelper()
        ->setCommandName('php-eval')
        ->setArguments(array(
          'code' => "return module_exists('{$moduleName}');",
        ))
        ->setOptions(array(
          'format' => 'string'
        ))
        ->mustRun(NULL, sprintf('Unable to determine module status of "%s"', $moduleName), FALSE);

      $cache[$moduleName] = $process->getOutput() ? TRUE : FALSE;
    }

    return $cache[$moduleName];
  }

}
