<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\DatabaseConnectionEntity.
 */

namespace hctom\DrupalUtils\Entity;

use Symfony\Component\Console\Question\Question;

/**
 * Provides a database connection entity.
 */
class DatabaseConnectionEntity extends InteractiveEntity implements DatabaseConnectionEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getCollation() {
    $defaultValue = 'utf8_general_ci';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Collation', $defaultValue), $defaultValue);

    $question->setAutocompleterValues($this->getCollationAutocompleterValues());

    return $this->ask($question);
  }

  /**
   * Return database collation autocompleter values.
   *
   * @return array
   *   The autocompleter values.
   */
  protected function getCollationAutocompleterValues() {
    return array(
      'utf8_general_ci',
    );
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
  public function getDatabaseName() {
    $defaultValue = '';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Database name', $defaultValue), $defaultValue);
    $question->setValidator(function($answer) {
      if (!isset($answer) || strlen(trim($answer)) === 0) {
        throw new \RuntimeException('Database name is required.');
      }

      return $answer;
    });

    return $this->ask($question);
  }

  /**
   * {@inheritdoc}
   */
  public function getDriver() {
    $defaultValue = 'mysql';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Driver', $defaultValue), $defaultValue);

    $question->setAutocompleterValues($this->getDriverAutocompleterValues());

    return $this->ask($question);
  }

  /**
   * Return database driver autocompleter values.
   *
   * @return array
   *   The autocompleter values.
   */
  protected function getDriverAutocompleterValues() {
    return array(
      'mysql',
      'sqlite',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getHost() {
    $defaultValue = 'localhost';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Host', $defaultValue), $defaultValue);

    $question->setAutocompleterValues($this->getHostAutocompleterValues());

    return $this->ask($question);
  }

  /**
   * Return database server host autocompleter values.
   *
   * @return array
   *   The autocompleter values.
   */
  public function getHostAutocompleterValues() {
    return array(
      'localhost',
      '127.0.0.1',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIdentifier() {
    return $this->getDatabaseKey() . '|' . $this->getTargetKey();
  }

  /**
   * {@inheritdoc}
   */
  public function getPassword() {
    $defaultValue = '';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Password', $defaultValue), $defaultValue);
    $question->setHidden(TRUE);

    return $this->ask($question);
  }

  /**
   * {@inheritdoc}
   */
  public function getPort() {
    $defaultValue = 3306;
    $question = new Question($this->getFormatterHelper()->formatQuestion('Port', $defaultValue), $defaultValue);

    $question->setAutocompleterValues($this->getPortAutocompleterValues());

    return $this->ask($question);
  }

  /**
   * Return database server port autocompleter values.
   *
   * @return array
   *   The autocompleter values.
   */
  protected function getPortAutocompleterValues() {
    return array(
      3306,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTableNamePrefix() {
    $defaultValue = '';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Table name prefix', $defaultValue), $defaultValue);

    return $this->ask($question);
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetKey() {
    return 'default';
  }

  /**
   * {@inheritdoc}
   */
  public function getUsername() {
    $defaultValue = 'root';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Username', $defaultValue), $defaultValue);

    $question->setAutocompleterValues($this->getUsernameAutocompleterValues());

    return $this->ask($question);
  }

  /**
   * Return database username autocompleter values.
   *
   * @return array
   *   The autocompleter values.
   */
  protected function getUsernameAutocompleterValues() {
    return array(
      'root',
    );
  }

}
