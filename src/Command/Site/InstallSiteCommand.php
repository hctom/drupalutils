<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Site\InstallSiteCommand.
 */

namespace hctom\DrupalUtils\Command\Site;

use hctom\DrupalUtils\Command\TaskedCommand;
use hctom\DrupalUtils\Task\Database\EnsureDatabaseSettingsTask;
use hctom\DrupalUtils\Task\Environment\EnsureEnvSettingsTask;
use hctom\DrupalUtils\Task\Environment\SymlinkEnvHtaccessTask;
use hctom\DrupalUtils\Task\Environment\SymlinkEnvSettingsTask;
use hctom\DrupalUtils\Task\Filesystem\EnsurePublicFilesDirectoryTask;
use hctom\DrupalUtils\Task\Filesystem\EnsureSiteDirectoryTask;
use hctom\DrupalUtils\Task\Site\InstallSiteTask;

/**
 * Provides a command to perform a basic installation of a Drupal site.
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

    // Add default tasks.
    $this->getTaskList()
      ->addMultiple(array(
        new EnsureSiteDirectoryTask(),
        new EnsureEnvSettingsTask(),
        new SymlinkEnvHtaccessTask(),
        new SymlinkEnvSettingsTask(),
        new EnsureDatabaseSettingsTask(),
        new InstallSiteTask(),
        new EnsurePublicFilesDirectoryTask(),
      ));
  }

}
