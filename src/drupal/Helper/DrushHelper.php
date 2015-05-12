<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrushHelper.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\Helper;

/**
 * Provides helpers for the external Drush binary.
 */
class DrushHelper extends Helper {

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
   * {@inheritdoc}
   */
  public function getName() {
    return 'drush';
  }

  /**
   * Return Drush version.
   *
   * @return string
   *   The Drush version.
   */
  public function getVersion() {
    static $version;

    if (!isset($version)) {
      $version = trim($this->getDrushProcessHelper()
        ->setCommandName('version')
        ->setOptions(array(
          'pipe' => TRUE,
        ))
        ->mustRun(NULL, 'Unable to determine Drush version', FALSE)
        ->getOutput());
    }

    return $version;
  }

}
