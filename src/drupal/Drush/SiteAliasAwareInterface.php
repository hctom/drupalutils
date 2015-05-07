<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\SiteAliasAwareInterface.
 */

namespace hctom\DrupalUtils\Drush;

/**
 * Drupal utilities: Drush site alias aware interface.
 */
interface SiteAliasAwareInterface {

  /**
   * Set Drush site alias.
   *
   * @param string $siteAlias
   *   The Drush site alias.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setSiteAlias($siteAlias);

}
