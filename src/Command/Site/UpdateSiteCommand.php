<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\Site\UpdateSiteCommand.
 */

namespace hctom\DrupalUtils\Command\Site;

use hctom\DrupalUtils\Command\TaskedCommand;
use hctom\DrupalUtils\Task\Database\EnsureDatabaseSettingsTask;
use hctom\DrupalUtils\Task\Database\UpdateDatabaseTask;
use hctom\DrupalUtils\Task\Environment\EnsureEnvSettingsTask;
use hctom\DrupalUtils\Task\Environment\SymlinkEnvHtaccessTask;
use hctom\DrupalUtils\Task\Environment\SymlinkEnvSettingsTask;
use hctom\DrupalUtils\Task\Filesystem\EnsurePrivateFilesDirectoryTask;
use hctom\DrupalUtils\Task\Filesystem\EnsurePublicFilesDirectoryTask;
use hctom\DrupalUtils\Task\Filesystem\EnsureSiteDirectoryTask;
use hctom\DrupalUtils\Task\Filesystem\EnsureTemporaryFilesDirectoryTask;

/**
 * Provides a command to perform basic updates on a Drupal site.
 */
class UpdateSiteCommand extends TaskedCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('site:update')
      ->setDescription('Update a Drupal site.')
      ->setHelp(
<<<EOT
The <info>%command.name%</info> command performs updates on a Drupal site:

<info>%command.full_name%</info>
EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function doInitializeTaskList() {
    parent::doInitializeTaskList();

    $this->getTaskList()
      ->addMultiple(array(
        new EnsureSiteDirectoryTask(),
        new EnsureEnvSettingsTask(),
        new SymlinkEnvSettingsTask(),
        new SymlinkEnvHtaccessTask(),
        new EnsureDatabaseSettingsTask(),
        new EnsureTemporaryFilesDirectoryTask(),
        new EnsurePublicFilesDirectoryTask(),
        new EnsurePrivateFilesDirectoryTask(),
        new UpdateDatabaseTask(),
      ));
  }

}
