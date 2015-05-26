<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Database\EnsureDefaultDatabaseSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Database;

/**
 * Provides a task command to ensure the environment specific database settings
 * file (settings/db.ENVIRONMENT.inc).
 */
class EnsureDefaultDatabaseSettingsTask extends EnsureDatabaseSettingsTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:database:settings:default:default');
  }

  /**
   * {@inheritdoc}
   */
  public function getDatabaseKey() {
    return 'default';
  }

  /**
   * {@inheritdoc}
   */
  public function getDatabaseTargetKey() {
    return 'default';
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Configure default database connection settings';
  }

}
