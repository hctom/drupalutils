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
  protected function buildOptions() {
    $options = parent::buildOptions();

    // Assume 'yes' as answer to all prompts.
    $options['yes'] = '--yes';

    // Display even more information, including internal messages?
    if ($this->getOutput()->isDebug()) {
      $options['debug'] = '--debug';
    }

    // Display extra information about the command?
    elseif ($this->getOutput()->isVeryVerbose()) {
      $options['verbose'] = '--verbose';
    }

    return $options;
  }

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
