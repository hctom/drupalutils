<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Site\InitialSiteSetupCommand.
 */

namespace hctom\DrupalUtils\Command\Site;

use hctom\DrupalUtils\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a command to perform the initial setup of a Drupal site.
 */
class InitialSiteSetupCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('init:site')
      ->setDescription('Initially setup a Drupal site.')
      ->setHelp(
<<<EOT
The <info>%command.name%</info> command performs the initial setup of a Drupal site:

<info>%command.full_name%</info>
EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    foreach ($this->getSetupCommandNames() as $commandName) {
      // Determine command.
      $command = $this->getApplication()->find($commandName);

      // Create sub-input.
      $subInput = clone $input;
      $subInput->setArgument('command', $commandName);

      // Unable to run sub-command.
      if (($exitCode = $command->run($subInput, $output))) {
        return $exitCode;
      }
    }
  }

  /**
   * Return initial setup command names.
   *
   * @return array
   *   An array of names for the commands that should be run during initial
   *   setup.
   */
  protected function getSetupCommandNames() {
    return array(
      'site:install',
      'site:update',
    );
  }

}
