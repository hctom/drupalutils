<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\SiteAliasAwareInterface.
 */

namespace hctom\DrupalUtils\Drush;

/**
 * Should be implemented by classes that depend on the Drush site alias.
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
