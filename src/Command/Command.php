<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Console\Application;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareTrait;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command base class.
 */
abstract class Command extends SymfonyConsoleCommand implements DrushSiteAliasAwareInterface {

  use DrushSiteAliasAwareTrait;

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    // Save Drush site alias.
    $this->setDrushSiteAlias($input->getOption(Application::INPUT_OPTION_DRUSH_SITE_ALIAS));
  }

}
