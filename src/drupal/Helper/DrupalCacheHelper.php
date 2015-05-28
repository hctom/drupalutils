<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrupalCacheHelper.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\Helper;

/**
 * Provides helpers for Drupal caches.
 */
class DrupalCacheHelper extends Helper {

  /**
   * Flush cache(s).
   *
   * @param $type
   *   The type of the cache to flush. Pass 'all' to flush all caches (is the
   *   default).
   *
   * @return null|int
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function flush($type = 'all') {
    return $this->getDrushProcessHelper()
      ->setCommandName('cache-clear')
      ->setArguments(array(
        'type' => $type,
      ))
      ->mustRun('Cleared cache(s)', 'Unable to clear cache(s)')
      ->getExitCode();
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
   * {@inheritdoc}
   */
  public function getName() {
    return 'drupal_cache';
  }

}
