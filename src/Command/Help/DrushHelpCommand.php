<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Help\DrushHelpCommand.
 */

namespace hctom\DrupalUtils\Command\Help;

use hctom\DrupalUtils\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command class: Drush help.
 */
class DrushHelpCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('help:drush')
      ->setDescription('Display help for a given Drush command.')
      ->setDefinition(array(
        new InputArgument('command_name', InputArgument::OPTIONAL, 'The name of the Drush command to get help for.'),
      ))
      ->setHelp(
<<<EOT
The <info>%command.name%</info> command displays help for a given Drush command:

<info>%command.full_name% help:drush drush:cache-clear</info>
EOT
      )
    ;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if (!$input->getArgument('command_name')) {
      $command = $this->getApplication()->find('list:drush');

      $subInput = new ArrayInput(array(
        'command' => 'list:drush',
      ));

      return $command->run($subInput, $output);
    }

    return $this->runDrush('help', array(
      'command_name' => $input->getArgument('command_name'),
    ), $output);
  }

}
