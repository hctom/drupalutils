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
 * Drupal utilities command class: Install site.
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
The <info>%command.name%</info> command installs a Drupal site:

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
