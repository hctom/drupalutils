<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\DrushSiteAliasAwareTrait.
 */

namespace hctom\DrupalUtils\Drush;

/**
 * Drupal utilities Drush site alias aware trait.
 */
trait DrushSiteAliasAwareTrait {

  /**
   * Drush site alias
   *
   * @var string
   */
  private $drushSiteAlias;

  /**
   * Return Drush site alias.
   *
   * @return string
   *   The Drush site alias (defaults to '@none').
   */
  public function getDrushSiteAlias() {
    return !empty($this->drushSiteAlias) ? $this->drushSiteAlias : '@none';
  }

  /**
   * Set Drush site alias.
   *
   * @param string
   *   The Drush site alias.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setDrushSiteAlias($drushSiteAlias) {
    $this->drushSiteAlias = '@' . ltrim($drushSiteAlias, '@');

    return $this;
  }

}
