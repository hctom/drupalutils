<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Command.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Console\Application;
use hctom\DrupalUtils\Drush\Drush;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\DrushSiteAliasAwareTrait;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command base class.
 */
abstract class Command extends SymfonyConsoleCommand implements DrushSiteAliasAwareInterface {

  use DrushSiteAliasAwareTrait;

  /**
   * Return Drush.
   *
   * @return Drush
   *   The Drush object.
   */
  public function drush() {
    return new Drush($this->getDrushSiteAlias());
  }

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    // Save Drush site alias.
    $this->setDrushSiteAlias($input->getOption(Application::INPUT_OPTION_DRUSH_SITE_ALIAS));
  }

  /**
   * Run Drush command.
   *
   * @param string $drushCommandName
   *   The name of the Drush command to execute.
   * @param array $drushInputArray
   *   The Drush input.
   * @param OutputInterface $output
   *   The output.
   *
   * @return int
   *   0 if everything went fine, or an error code.
   */
  protected function runDrush($drushCommandName, array $drushInputArray, OutputInterface $output) {
    // Extend input with static values.
    $drushInputArray = array_merge(array(
      'command' => $drushCommandName,
      '--' . Application::INPUT_OPTION_DRUSH_SITE_ALIAS => $this->getDrushSiteAlias()
    ), $drushInputArray);

    return $this->drush()
      ->run(new ArrayInput($drushInputArray), $output);
  }

}
