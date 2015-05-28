<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Environment\EnsureEnvSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Environment;

use hctom\DrupalUtils\Task\Filesystem\EnsureSettingsFileTask;

/**
 * Provides a task command to create an environment specific settings.php file
 * (if not exists).
 */
class EnsureEnvSettingsTask extends EnsureSettingsFileTask {

  /**
   * Build includes.
   *
   * @return array
   *   An array of includes to add to the file.
   */
  protected function buildIncludes() {
    $settingsBasePath = $this->getSiteSettingsDirectory();
    $environment = $this->getDrupalHelper()->getEnvironment();

    return array(
      // Required includes.
      'default.settings.php' => array(
        'comment' => 'Drupal: Default settings.',
        'path' => 'sites/default/default.settings.php',
      ),

      // Optional includes.
      'db.ENVIRONMENT.inc' => array(
        'path' => $settingsBasePath . DIRECTORY_SEPARATOR . 'db.' . $environment . '.inc',
        'comment' => 'Environment: Database connections.',
      ),
      'master.ENVIRONMENT.inc' => array(
        'path' => $settingsBasePath . DIRECTORY_SEPARATOR . 'master.' . $environment . '.inc',
        'comment' => 'Environment: Master modules.',
      ),
      'conf.ENVIRONMENT.inc' => array(
        'path' => $settingsBasePath . DIRECTORY_SEPARATOR . 'conf.' . $environment . '.inc',
        'comment' => 'Environment: Configuration variables.',
      ),
      'settings.shared.inc' => array(
        'path' => $settingsBasePath . DIRECTORY_SEPARATOR . 'settings.shared.inc',
        'comment' => 'Shared: Custom settings.',
      ),
      'settings.ENVIRONMENT.inc' => array(
        'path' => $settingsBasePath . DIRECTORY_SEPARATOR . 'settings.' . $environment . '.inc',
        'comment' => 'Environment: Custom settings.',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:environment:settings:ensure');
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings.' . $this->getDrupalHelper()->getEnvironment() . '.php';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateName() {
    return '@drupalutils/settings.ENVIRONMENT.php.twig';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateVariables() {
    $variables = array();

    // Includes.
    foreach ($this->buildIncludes() as $key => $include) {
      // Ensure relative paths for all includes.
      $include['path'] = $this->getFilesystemHelper()->makePathRelative($include['path']);

      // Add include to variables.
      $variables['includes'][$key] = $include;
    }

    return $variables;
  }

  /**
   * Return task title.
   *
   * @return string
   *   The human-readable title of the task.
   */
  public function getTitle() {
    return 'Ensure environment specific settings.php file';
  }

}
