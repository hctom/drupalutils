<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\DatabaseConnectionEntityInterface.
 */

namespace hctom\DrupalUtils\Entity;

/**
 * Should be implemented by database connection entity classes.
 */
interface DatabaseConnectionEntityInterface extends EntityInterface {

  /**
   * Return database collation.
   *
   * @return string
   *   The database collation.
   */
  public function getCollation();

  /**
   * Return database key.
   *
   * @return string
   *   The key of the database in the $databases array (defaults to 'default').
   */
  public function getDatabaseKey();

  /**
   * Return database name.
   *
   * @return string
   *   The database name.
   */
  public function getDatabaseName();

  /**
   * Return database driver.
   *
   * @return string
   *   The database driver.
   */
  public function getDriver();

  /**
   * Return database server host.
   *
   * @return string
   *   The database server host.
   */
  public function getHost();

  /**
   * Return database password.
   *
   * @return string
   *   The database password.
   */
  public function getPassword();

  /**
   * Return database server port.
   *
   * @return string
   *   The database server port.
   */
  public function getPort();

  /**
   * Return database table name prefix.
   *
   * @return string
   *   The database table name prefix.
   */
  public function getTableNamePrefix();

  /**
   * Return database target key.
   *
   * @return string
   *   The key of the database target in the $databases array (defaults to
   *   'default').
   */
  public function getTargetKey();

  /**
   * Return database username.
   *
   * @return string
   *   The database username.
   */
  public function getUsername();

}
