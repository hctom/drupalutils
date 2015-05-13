<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrushSiteAliasHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Drush\SiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\SiteAliasAwareTrait;
use hctom\DrupalUtils\Drush\SiteAliasConfig;
use hctom\DrupalUtils\Output\OutputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for the Drupal site alias.
 */
class DrushSiteAliasHelper extends Helper implements OutputAwareInterface, SiteAliasAwareInterface {

  use OutputAwareTrait;
  use SiteAliasAwareTrait;

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
   * Return Drush site alias configuration.
   *
   * @return SiteAliasConfig
   *   An object containing all configuration associated to the current Drush
   *   site alias.
   *
   * @throws RuntimeException
   */
  public function getConfig() {
    static $config = array();

    $siteAlias = $this->getSiteAlias();
    $siteAliasWithoutAtChar = ltrim($siteAlias, '@');

    if (!isset($config[$siteAlias])) {
      $process = $this->getDrushProcessHelper()
        ->setCommandName('site-alias')
        ->setArguments(array(
          'site' => $siteAlias,
        ))
        ->setOptions(array(
          'format' => 'json',
          'full' => TRUE,
        ))
        ->run($this->getOutput()->isVerbose() ? 'Loaded Drush site alias configuration' : NULL, 'Unable to load Drush site alias configuration', FALSE);

      // Parse site configuration.
      if (!($data = json_decode($process->getOutput()))) {
        throw new RuntimeException(sprintf('Unable to parse %s Drush site alias details', $siteAlias));
      }

      // Does not contain site configuration?
      if (!isset($data->{$siteAliasWithoutAtChar})) {
        throw new RuntimeException(sprintf('Unable to locate %s Drush site alias details', $siteAlias));
      }

      // Create configuration object.
      $config[$siteAlias] = new SiteAliasConfig($data->{$siteAliasWithoutAtChar});
    }

    return $config[$siteAlias];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drush_site_alias';
  }

}
