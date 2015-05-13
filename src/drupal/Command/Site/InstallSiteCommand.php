<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Site\InstallSiteCommand.
 */

namespace hctom\DrupalUtils\Command\Site;

use hctom\DrupalUtils\Command\TaskedCommand;
use hctom\DrupalUtils\Task\Site\InstallSiteTask;
use hctom\DrupalUtils\Task\User\LoginTask;

/**
 * Provides a command to perform the installation of a Drupal site.
 */
class InstallSiteCommand extends TaskedCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('site:install')
      ->setDescription('Install a Drupal site.')
      ->setHelp(
<<<EOT
The <info>%command.name%</info> command performs the installation of a Drupal site:

<info>%command.full_name%</info>
EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getTasks() {
    $defaultTasks = parent::getTasks();

    $defaultTasks[] = new InstallSiteTask();
    $defaultTasks[] = new LoginTask();

    return $defaultTasks;
  }

}
