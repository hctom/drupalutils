<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\SiteAliasAwareTrait.
 */

namespace hctom\DrupalUtils\Drush;

/**
 * Provides methods for working with a Drush site alias.
 */
trait SiteAliasAwareTrait {

  /**
   * Drush site alias.
   *
   * @var string
   */
  private $siteAlias;

  /**
   * Return Drush site alias.
   *
   * @return string
   *   The Drush site alias.
   *
   * @throws \RuntimeException
   */
  public function getSiteAlias() {
    if (!$this->siteAlias) {
      throw new \RuntimeException('No Drush site alias has been specified');
    }

    return $this->siteAlias;
  }

  /**
   * {@inheritdoc}
   */
  public function setSiteAlias($siteAlias) {
    $this->siteAlias = '@' . ltrim($siteAlias, '@');

    return $this;
  }

}
