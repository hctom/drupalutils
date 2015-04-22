<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\DrushSiteAliasAwareInterface.
 */

namespace hctom\DrupalUtils\Drush;

/**
 * Drupal utilities Drush site alias aware interface.
 */
interface DrushSiteAliasAwareInterface {

  /**
   * Return Drush site alias.
   *
   * @return string
   *   The Drush site alias.
   */
  public function getDrushSiteAlias();

  /**
   * Set Drush site alias.
   *
   * @param string
   *   The Drush site alias.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setDrushSiteAlias($drushSiteAlias);

}
