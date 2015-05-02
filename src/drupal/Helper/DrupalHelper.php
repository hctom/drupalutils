<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrupalHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrushWrapper\Helper\DrushHelper;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Drupal utilities Drupal helper class.
 */
class DrupalHelper extends Helper {

  /**
   * Return Drush helper.
   *
   * @return DrushHelper
   *   The Drush helper object.
   */
  protected function drush() {
    return $this->getHelperSet()->get('drush');
  }

  /**
   * Ensure absolute path.
   *
   * @param $path
   *   The path to rewrite (if necessary).
   *
   * @return string
   *   The absolute path.
   */
  protected function ensureAbsolutePath($path) {
    if (!$this->fileSystem()->isAbsolutePath($path)) {
      // TODO Ensure realpath.
      $path = $this->getRootDirectoryPath() . DIRECTORY_SEPARATOR . $path;
    }

    return $path;
  }

  /**
   * Return file system helper.
   *
   * @return Filesystem
   *   The file system helper object.
   */
  protected function fileSystem() {
    return new Filesystem();
  }

  /**
   * Return Drupal core status.
   *
   * @return \stdClass
   *   An object containing information about the core status.
   */
  protected function getCoreStatus() {
    static $status;

    if (!isset($status)) {
      // Fetch core status via Drush.
      $process = $this->drush()
        ->runProcess('core-status', array(), array('format' => 'json'), new NullOutput());

      // Unable to parse Drupal core status information?
      if (!($status = json_decode($process->getOutput()))) {
        throw new \RuntimeException('Unable to parse Drupal core status information.');
      }
    }

    return $status;
  }

  /**
   * Return Drupal's file directory path.
   *
   * @return string
   *   The absolute path of the file directory.
   *
   * @throws \RuntimeException
   */
  public function getFilesDirectoryPath() {
    if (!property_exists($this->getCoreStatus(), 'files')) {
      throw new \RuntimeException("Unable to determine Drupal's file directory path.");
    }

    return $this->ensureAbsolutePath($this->getCoreStatus()->files);
  }

  /**
   * Return environment indicator.
   *
   * @return string
   *   The environment indicator.
   *
   * @throws \RuntimeException
   */
  public function getEnvironment() {
    $details = $this->drush()
      ->getSiteAliasDetails();

    // No Drupal utilities configuration found?
    if (!property_exists($details, 'drupalutils')) {
      throw new \RuntimeException('Unable to determine Drupal environment indicator.');
    }

    // No environment indicator set?
    elseif (!property_exists($details->drupalutils, 'environment') || empty($details->drupalutils->environment)) {
      throw new \RuntimeException('No environment indicator has been set for Drupal.');
    }

    return $details->drupalutils->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drupal';
  }

  /**
   * Return Drupal's root path.
   *
   * @return string
   *   The absolute path of the docroot.
   *
   * @throws \RuntimeException
   */
  public function getRootDirectoryPath() {
    if (!property_exists($this->getCoreStatus(), 'root')) {
      throw new \RuntimeException("Unable to determine Drupal's root path.");
    }

    return $this->getCoreStatus()->root;
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
    if (!property_exists($this->getCoreStatus(), 'site')) {
      throw new \RuntimeException("Unable to determine Drupal's site path.");
    }

    return $this->ensureAbsolutePath($this->getCoreStatus()->site);
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
    if (!property_exists($this->getCoreStatus(), 'temp')) {
      throw new \RuntimeException("Unable to determine Drupal's temporary files directory path.");
    }

    return $this->ensureAbsolutePath($this->getCoreStatus()->temp);
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
    if (!property_exists($this->getCoreStatus(), 'drupal-version')) {
      throw new \RuntimeException('Unable to determine Drupal version.');
    }

    return $this->getCoreStatus()->{'drupal-version'};
  }

}
