<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Help\DrushListCommand.
 */

namespace hctom\DrupalUtils\Command\Help;

use hctom\DrupalUtils\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command class: List available Drush commands.
 */
class DrushListCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('list:drush')
      ->setDescription('List all available Drush commands.')
      ->setHelp(
<<<EOT
The <info>%command.name%</info> command lists all available Drush commands:

<info>%command.full_name%</info>

' . $this->getNotCallableNotice() . '
EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $exitCode = $this->runDrush('list', array(
      'namespace' => 'drush',
    ), $output);

    if (!$exitCode) {
      $output->writeln('');
      $output->writeln($this->getNotCallableNotice());
    }

    return $exitCode;
  }

  /**
   * Return notice about uncallable Drush commands.
   *
   * @return string
   *   The notice text.
   */
  protected function getNotCallableNotice() {
    return '<error> NOTE: </error> These commands can only be executed programatically and this list is just for reference.';
  }

}
