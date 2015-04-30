<?php

/**
 * @file
 * Contains hctom\DrushWrapper\Command\Command.
 */

namespace hctom\DrushWrapper\Command;

use hctom\DrushWrapper\Helper\DrushHelper;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command class: Drush command.
 */
class Command extends SymfonyConsoleCommand {

  /**
   * Return Drush helper.
   *
   * @return DrushHelper
   *   The Drush helper object.
   */
  public function drush() {
    return $this->getHelper('drush');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    // Determine command name.
    $commandName = $input->getArgument('command');

    // Process arguments.
    $arguments = $input->getArguments();
    unset($arguments['command']);

    return $this->drush()
      ->runProcess($commandName, $arguments, $input->getOptions())
      ->getExitCode();
  }

}
