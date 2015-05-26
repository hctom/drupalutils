<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Database\EnsureDatabaseSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Database;

use hctom\DrupalUtils\Task\Filesystem\EnsureSettingsFileTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Provides a task command to ensure the environment specific database settings
 * file (settings/db.ENVIRONMENT.inc).
 */
abstract class EnsureDatabaseSettingsTask extends EnsureSettingsFileTask {

  /**
   * Build parameters.
   *
   * @param InputInterface $input
   *   The input.
   * @param OutputInterface $output
   *   The output.
   * @return array
   *   The database connection parameters.
   */
  protected function buildParameters(InputInterface $input, OutputInterface $output) {
    $parameters = array(
      'driver' => $this->getDriver(),
      'database' => $this->getDatabaseName(),
      'username' => $this->getUsername(),
      'password' => $this->getPassword(),
      'host' => $this->getHost(),
      'port' => $this->getPort(),
      'prefix' => $this->getTableNamePrefix(),
      'collation' => $this->getCollation(),
    );

    // Ask questions (if necessary).
    foreach ($parameters as $parameterName => &$parameter) {
      if ($parameter instanceof Question) {
        $parameter = $this->getQuestionHelper()->ask($input, $output, $parameter);
      }
    }

    return $parameters;
  }

  /**
   * Return collation.
   *
   * @return Question
   *   A question object to prompt for the collation.
   */
  public function getCollation() {
    $defaultValue = 'utf8_general_ci';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Collation', $defaultValue), $defaultValue);

    $question->setAutocompleterValues(array(
      'utf8_general_ci',
    ));

    return $question;
  }

  /**
   * Return database key.
   *
   * @return string
   *   The key of the database in the $databases array.
   */
  abstract public function getDatabaseKey();

  /**
   * Return database name.
   *
   * @return Question
   *   A question object to prompt for the database name.
   */
  public function getDatabaseName() {
    $question = new Question('Database name: ');
    $question->setValidator(function($answer) {
      if (!isset($answer) || strlen(trim($answer)) === 0) {
        throw new \RuntimeException('Database name is required.');
      }

      return $answer;
    });

    return $question;
  }

  /**
   * Return database target key.
   *
   * @return string
   *   The key of the database target in the $databases array.
   */
  abstract public function getDatabaseTargetKey();

  /**
   * Return database driver.
   *
   * @return Question
   *   A question object to prompt for the database driver.
   */
  public function getDriver() {
    $defaultValue = 'mysql';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Driver', $defaultValue), $defaultValue);

    $question->setAutocompleterValues(array(
      'mysql',
      'sqlite',
    ));

    return $question;
  }

  /**
   * Return database server host.
   *
   * @return Question
   *   A question object to prompt for the database server host.
   */
  public function getHost() {
    $defaultValue = 'localhost';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Host', $defaultValue), $defaultValue);

    $question->setAutocompleterValues(array(
      'localhost',
      '127.0.0.1',
    ));

    return $question;
  }

  /**
   * Return database password.
   *
   * @return Question
   *   A question object to prompt for the database password.
   */
  public function getPassword() {
    $defaultValue = '';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Password', $defaultValue), $defaultValue);
    $question->setHidden(TRUE);

    return $question;
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'db.' . $this->getDrupalHelper()->getEnvironment() . '.inc';
  }

  /**
   * Return database server port.
   *
   * @return Question
   *   A question object to prompt for the database server port.
   */
  public function getPort() {
    $defaultValue = 3306;
    $question = new Question($this->getFormatterHelper()->formatQuestion('Port', $defaultValue), $defaultValue);

    $question->setAutocompleterValues(array(
      3306,
    ));

    return $question;
  }

  /**
   * Return database table name prefix.
   *
   * @return Question
   *   A question object to prompt for the database table name prefix.
   */
  public function getTableNamePrefix() {
    $defaultValue = '';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Table name prefix', $defaultValue), $defaultValue);

    return $question;
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
  protected function getTemplateVariables(InputInterface $input, OutputInterface $output) {
    $variables = array();
    $filesystem = $this->getFilesystemHelper();

    // Load current database settings.
    if ($filesystem->exists($this->getPath())) {
      $oldConfig = $filesystem->runPhpFileInIsolation($this->getPath());
      $databases = property_exists($oldConfig, 'databases') ? $oldConfig->databases : new \stdClass();
    }
    else {
      $databases = new \stdClass();
    }

    // Initialize database object (if not already).
    if (!isset($databases->{$this->getDatabaseKey()})) {
      $databases->{$this->getDatabaseKey()} = new \stdClass();
    }

    // Assign database connection settings.
    $databases->{$this->getDatabaseKey()}->{$this->getDatabaseTargetKey()} = $this->buildParameters($input, $output);

    // Add databases to variables (while ensuring 'array' variable type).
    $variables['dbs'] = json_decode(json_encode($databases), TRUE);

    return $variables;
  }

  /**
   * Return database username.
   *
   * @return Question
   *   A question object to prompt for the database username.
   */
  public function getUsername() {
    $defaultValue = 'root';
    $question = new Question($this->getFormatterHelper()->formatQuestion('Username', $defaultValue), $defaultValue);

    $question->setAutocompleterValues(array(
      'root',
    ));

    return $question;
  }

}
