<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrupalProjectHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for Drupal modules and themes.
 */
class DrupalProjectHelper extends Helper implements LoggerAwareInterface {

  use LoggerAwareTrait;

  /**
   * Project status: Enabled.
   */
  const STATUS_ENABLED = 'enabled';

  /**
   * Project status: Disabled.
   */
  const STATUS_DISABLED = 'disabled';

  /**
   * Project status: Not installed.
   */
  const STATUS_NOT_INSTALLED = 'not installed';

  /**
   * Project type: Module.
   */
  const TYPE_MODULE = 'module';

  /**
   * Project type: Theme.
   */
  const TYPE_THEME = 'theme';

  /**
   * Change project status.
   *
   * @param array $projectNames
   *   An array of project names.
   * @param $newStatus
   *   The status to set. Possible values:
   *     - static::STATUS_DISABLED
   *     - static::STATUS_ENABLED
   *     - static::STATUS_NOT_INSTALLED
   * @param array $options
   *   An optional array of process options.
   *
   * @return int|null
   *   NULL or 0 if everything went fine, or an error code.
   *
   * @throws InvalidArgumentException
   */
  protected function changeStatus(array $projectNames, $newStatus, array $options = array()) {
    switch ($newStatus) {
      case static::STATUS_DISABLED:
        $commandName = 'pm-disable';
        $successMessage = 'Disabled {projects}';
        $errorMessage = 'Unable to disable {projects}';
        break;

      case static::STATUS_ENABLED:
        $commandName = 'pm-enable';
        $successMessage = 'Enabled {projects}';
        $errorMessage = 'Unable to enable {projects}';
        break;

      case static::STATUS_NOT_INSTALLED:
        $commandName = 'pm-uninstall';
        $successMessage = 'Uninstalled {projects}';
        $errorMessage = 'Unable to uninstall {projects}';
        break;

      default;
        throw new InvalidArgumentException(sprintf('Invalid project status "%s"', $newStatus));
        break;
    }

    $messageContext = array(
      'projects' => $this->getFormatterHelper()->formatCommaSeparatedList($projectNames, 'code'),
    );

    $successMessage = array(
      $successMessage,
      $messageContext,
    );

    $errorMessage = array(
      $errorMessage,
      $messageContext,
    );

    return $this->getDrushProcessHelper()
      ->setCommandName($commandName)
      ->setArguments($projectNames)
      ->setOptions($options)
      ->run($successMessage, $errorMessage)
      ->getExitCode();
  }

  /**
   * Disable projects.
   *
   * @param array $projectNames
   *   An array of project names.
   *
   * @return int|null
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function disable(array $projectNames) {
    $formatter = $this->getFormatterHelper();

    foreach ($projectNames as $key => $projectName) {
      // Project is not installed.
      if ($this->isNotInstalled($projectName)) {
        unset ($projectNames[$key]);

        $this->getLogger()->always('{project} is not installed', array(
          'project' => $formatter->formatInlineCode($projectName),
        ));
      }

      // Project is disabled.
      elseif ($this->isDisabled($projectName)) {
        unset ($projectNames[$key]);

        $this->getLogger()->always('{project} is disabled', array(
          'project' => $formatter->formatInlineCode($projectName),
        ));
      }
    }

    if ($projectNames) {
      // Unable to disable project(s).
      if (($exitCode = $this->changeStatus($projectNames, static::STATUS_DISABLED))) {
        return $exitCode;
      }

      // Reload project list.
      $this->getProjectList(TRUE);
    }
  }

  /**
   * Enable projects.
   *
   * @param array $projectNames
   *   An array of project names.
   *
   * @return int|null
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function enable(array $projectNames, $downloadMissingDependencies = FALSE, $skipLibraryDownloads = TRUE) {
    foreach ($projectNames as $key => $projectName) {
      if ($this->isEnabled($projectName)) {
        unset ($projectNames[$key]);

        $this->getLogger()->always('{project} is enabled', array(
          'project' => $this->getFormatterHelper()->formatInlineCode($projectName),
        ));
      }
    }

    if ($projectNames) {
      // Build options.
      $options = array(
        'resolve-dependencies' => $downloadMissingDependencies,
        'skip' => $skipLibraryDownloads,
      );

      // Unable to enable project(s).
      if (($exitCode = $this->changeStatus($projectNames, static::STATUS_ENABLED, $options))) {
        return $exitCode;
      }

      // Reload project list.
      $this->getProjectList(TRUE);
    }
  }

  /**
   * Project exists?
   *
   * @param string $projectName
   *   The name of the project.
   *
   * @return bool
   *   Whether the project exists.
   */
  public function exists($projectName) {
    $projectList = $this->getProjectList();

    return property_exists($projectList, $projectName);
  }

  /**
   * Return Drush process helper.
   *
   * @return DrushProcessHelper
   *   The reset Drush process helper object.
   */
  protected function getDrushProcessHelper() {
    return $this->getHelperSet()->get('drush_process')->reset();
  }

  /**
   * Return Formatter helper.
   *
   * @return FormatterHelper
   *   The Formatter helper object.
   */
  protected function getFormatterHelper() {
    return $this->getHelperSet()->get('formatter');
  }

  /**
   * Return logger.
   *
   * @return LoggerInterface
   *   The logger.
   */
  protected function getLogger() {
    return $this->logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drupal_project';
  }

  /**
   * Return project list.
   *
   * @param bool $reset
   *   Whether to reload the project list.
   *
   * @return \stdClass
   *   An object containing information about all available projects.
   */
  protected function getProjectList($reset = FALSE) {
    static $projectList;

    if (!isset($projectList) || $reset) {
      $process = $this->getDrushProcessHelper()
        ->setCommandName('pm-list')
        ->setOptions(array(
          'format' => 'json',
        ))
        ->run(NULL, NULL, FALSE);

      if (($projectList = json_decode($process->getOutput())) === NULL) {
        throw new RuntimeException('Unable to parse project list');
      }
    }

    return $projectList;
  }

  /**
   * Project has specific status?
   *
   * @param string $projectName
   *   The name of the project.
   * @param string $status
   *   The status. Possible values:
   *     - static::STATUS_DISABLED
   *     - static::STATUS_ENABLED
   *     - static::STATUS_NOT_INSTALLED
   *
   * @return bool
   *   Whether the project has the specific status.
   *
   * @throws RuntimeException
   */
  protected function hasStatus($projectName, $status) {
    // Project does not exist.
    if (!$this->exists($projectName)) {
      throw new RuntimeException(sprint('Project "%s" not found', $projectName));
    }

    $project = $this->getProjectList()->{$projectName};

    $hasStatus = strtolower($project->status) === strtolower($status);

    return $hasStatus;
  }

  /**
   * Project is disabled?
   *
   * @param string $projectName
   *   The name of the project to check.
   *
   * @return bool
   *   Whether the project is disabled.
   */
  public function isDisabled($projectName) {
    return $this->hasStatus($projectName, static::STATUS_DISABLED);
  }

  /**
   * Project is enabled?
   *
   * @param string $projectName
   *   The name of the project to check.
   *
   * @return bool
   *   Whether the project is enabled.
   */
  public function isEnabled($projectName) {
    return $this->hasStatus($projectName, static::STATUS_ENABLED);
  }

  /**
   * Project is a module?
   *
   * @param string $projectName
   *   The name of the project to check.
   *
   * @return bool
   *   Whether the project is a module.
   */
  public function isModule($projectName) {
    return $this->isOfType($projectName, static::TYPE_MODULE);
  }

  /**
   * Project is not installed?
   *
   * @param string $projectName
   *   The name of the project to check.
   *
   * @return bool
   *   Whether the project is not installed.
   */
  public function isNotInstalled($projectName) {
    return $this->hasStatus($projectName, static::STATUS_NOT_INSTALLED);
  }

  /**
   * Project is of given type?
   *
   * @param string $projectName
   *   The name of the project to check.
   * @param string $type
   *   The type. Possible values:
   *     - static::TYPE_MODULE
   *     - static::TYPE_THEME
   *
   * @return bool
   *   Whether the project is a theme.
   *
   * @throws InvalidArgumentException, RuntimeException
   */
  public function isOfType($projectName, $type) {
    // Project does not exist.
    if (!$this->exists($projectName)) {
      throw new RuntimeException(sprint('Project "%s" not found', $projectName));
    }

    switch ($type) {
      case static::TYPE_MODULE:
      case static::TYPE_THEME:
        return strtolower($this->getProjectList()->{$projectName}->type) === strtolower($type);
        break;

      default:
        throw new InvalidArgumentException(sprintf('Invalid project type "%s"', $type));
        break;
    }
  }

  /**
   * Project is a theme?
   *
   * @param string $projectName
   *   The name of the project to check.
   *
   * @return bool
   *   Whether the project is a theme.
   */
  public function isTheme($projectName) {
    return $this->isOfType($projectName, static::TYPE_THEME);
  }

  /**
   * Uninstall projects.
   *
   * @param array $projectNames
   *   An array of project names.
   *
   * @return int|null
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function uninstall(array $projectNames) {
    $disable = array();
    $formatter = $this->getFormatterHelper();

    foreach ($projectNames as $key => $projectName) {
      // Project is not installed.
      if ($this->isNotInstalled($projectName)) {
        unset($projectNames[$key]);

        $this->getLogger()->always('{project} is not installed', array(
          'project' => $formatter->formatInlineCode($projectName),
        ));
      }

      // Project is a theme.
      elseif ($this->isTheme($projectName)) {
        unset($projectNames[$key]);

        // Themes only need to be disabled.
        if ($this->isEnabled($projectName)) {
          $disable[] = $projectName;

          continue;
        }

        $this->getLogger()->always('{project} is not installed', array(
          'project' => $formatter->formatInlineCode($projectName),
        ));
      }

      // Project is enabled.
      elseif ($this->isEnabled($projectName)) {
        $disable[] = $projectName;
      }
    }

    // Some project(s) need to be disabled first.
    if ($disable) {
      // Unable to disable project(s).
      if (($exitCode = $this->disable($disable))) {
        return $exitCode;
      }
    }

    if ($projectNames) {
      // Unable to uninstall project(s).
      if (($exitCode = $this->changeStatus($projectNames, static::STATUS_NOT_INSTALLED))) {
        return $exitCode;
      }

      // Reload project list.
      $this->getProjectList(TRUE);
    }
  }

}
