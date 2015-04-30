<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Drush\ListCommand.
 */

namespace hctom\DrupalUtils\Command\Drush;

use hctom\DrupalUtils\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command class: List available Drush commands.
 */
class ListCommand extends Command {

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

%command.not_callable_notice%
EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $exitCode = $this->drush()
      ->runCommand(array(
        'command' => 'list',
        'namespace' => 'drush',
      ), $input);

    // Display notice about uncallable Drush commands.
    if (!$exitCode) {
      $output->writeln('');
      $output->writeln($this->getNotCallableNotice());
    }

    return $exitCode;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessedHelp() {
    $help = parent::getProcessedHelp();

    // Register additional placeholders.
    $placeholders = array(
      '%command.not_callable_notice%',
    );

    // Register additional replacements.
    $replacements = array(
      $this->getNotCallableNotice(),
    );

    return str_replace($placeholders, $replacements, $help);
  }

  /**
   * Return notice about uncallable Drush commands.
   *
   * @return string
   *   The notice text.
   */
  protected function getNotCallableNotice() {
    return '<error> NOTE: </error> The listed commands can only be executed programatically and the list is just for reference.';
  }

}
