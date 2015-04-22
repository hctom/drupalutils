<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Drush\DrushSiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareTrait;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;

/**
 * Drupal utilities command base class.
 */
abstract class Command extends SymfonyConsoleCommand implements DrushSiteAliasAwareInterface {

  use DrushSiteAliasAwareTrait;

}
