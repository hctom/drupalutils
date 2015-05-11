<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrushProcessHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Drush\SiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\SiteAliasAwareTrait;

/**
 * Provides helpers to run external Drush processes.
 */
class DrushProcessHelper extends ProcessHelper implements SiteAliasAwareInterface {

  use SiteAliasAwareTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drush_process';
  }

  /**
   * {@inheritdoc}
   */
  protected function getProcessBuilder() {
    $processBuilder = parent::getProcessBuilder();

    // Override process builder prefix.
    $processBuilder->setPrefix(array(
      'drush',
      $this->getSiteAlias(),
      $this->getCommandName(),
    ));

    return $processBuilder;
  }

}
