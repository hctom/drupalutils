<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Database\EnsureDatabaseSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Database;

use hctom\DrupalUtils\Entity\DatabaseConnectionEntity;
use hctom\DrupalUtils\Entity\DatabaseConnectionEntityInterface;
use hctom\DrupalUtils\Entity\EntityAwareTrait;
use hctom\DrupalUtils\Entity\EntityInterface;
use hctom\DrupalUtils\Entity\InteractiveEntityAwareInterface;
use hctom\DrupalUtils\Input\InputAwareTrait;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use hctom\DrupalUtils\Task\Filesystem\EnsureSettingsFileTask;

/**
 * Provides a task command to ensure the environment specific database settings
 * file (settings/db.ENVIRONMENT.inc).
 */
class EnsureDatabaseSettingsTask extends EnsureSettingsFileTask implements InteractiveEntityAwareInterface {

  use EntityAwareTrait;
  use InputAwareTrait;
  use OutputAwareTrait;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:database:settings:ensure');
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'db.' . $this->getDrupalHelper()->getEnvironment() . '.inc';
  }

  /**
   * {@inheritdoc}
   */
  public function getSkipIfExists() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateName() {
    return '@drupalutils/db.ENVIRONMENT.inc.twig';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateVariables() {
    $variables = array();

    /* @var DatabaseConnectionEntity $databaseConnection */
    foreach ($this->getEntities() as $databaseConnection) {
      $variables['databases'][$databaseConnection->getDatabaseKey()][$databaseConnection->getTargetKey()] = array(
        'driver' => $databaseConnection->getDriver(),
        'database' => $databaseConnection->getDatabaseName(),
        'username' => $databaseConnection->getUsername(),
        'password' => $databaseConnection->getPassword(),
        'host' => $databaseConnection->getHost(),
        'port' => $databaseConnection->getPort(),
        'prefix' => $databaseConnection->getTableNamePrefix(),
        'collation' => $databaseConnection->getCollation(),
      );
    }

    return $variables;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Ensure database connection settings';
  }

  /**
   * {@inheritdoc}
   */
  public function initializeEntities() {
    $this
      // Register default database connection.
      ->registerEntities(array(
        new DatabaseConnectionEntity(),
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function validateEntity(EntityInterface $entity) {
    if (!$entity instanceof DatabaseConnectionEntityInterface) {
      throw new \InvalidArgumentException('Invalid entity given');
    }
  }

}
