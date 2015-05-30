<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Master\EnsureMasterModulesTask.
 */

namespace hctom\DrupalUtils\Task\Master;

use hctom\DrupalUtils\Task\Filesystem\EnsureSettingsFileTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task base class for ensuring master modules.
 */
abstract class EnsureMasterModulesTask extends EnsureSettingsFileTask {

  /**
   * Build module list.
   *
   * @param $modules
   *   An array of module names.
   *
   * @return array
   *   The processed array of module names with the following changes applied:
   *     - Filtered duplicate values.
   *     - Made array associative (key and value = module name).
   *     - Sorted array.
   */
  protected function buildModuleList($modules) {
    // Filter duplicate values.
    $modules = array_unique($modules);

    // Make module list associative.
    $modules = array_combine($modules, $modules);

    // Sort module list.
    ksort($modules);

    return $modules;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:site:master:ensure:modules');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    if (!parent::execute($input, $output)) {
      $exitCode = $this->getDrushProcessHelper()
        ->setCommandName('master-ensure-modules')
        ->setOptions(array(
          'no-cache-clear' => $this->getNoCacheClear(),
          'no-disable' => $this->getNoDisable(),
          'no-uninstall' => $this->getNoUninstall(),
          'scope' => $this->getScope(),
          'skip-scope-validation' => $this->getSkipScopeValidation(),
          'yes' => TRUE,
        ))
        ->mustRun('Ensured master modules', 'Unable to ensure master modules')
        ->getExitCode();

      // Reload project list (if no error)
      if (!$exitCode) {
        $this->getProjectHelper()->getProjectList(TRUE);
      }

      return $exitCode;
    }
  }

  /**
   * Return base modules.
   *
   * @return array
   *   The names of modules that should be enabled in all scopes.
   */
  public function getBaseModules() {
    return array();
  }

  /**
   * Return 'Master' configuration definition version.
   *
   * @return int
   *   The version of the used configuration definition.
   */
  public function getMasterVersion() {
    return 3;
  }

  /**
   * Return modules.
   *
   * @return array
   *   The names of modules that should be enabled in the specified scope.
   */
  public function getModules() {
    return array();
  }

  /**
   * Flushing of caches should be avoided?
   *
   * @return bool
   *   Whether the flushing of caches should be avoided.
   */
  public function getNoCacheClear() {
    return FALSE;
  }

  /**
   * No modules should be disabled?
   *
   * @return bool
   *   Whether no module should be disabled.
   */
  public function getNoDisable() {
    return FALSE;
  }

  /**
   * No modules should be uninstalled?
   *
   * @return bool
   *   Whether no module should be uninstalled.
   */
  public function getNoUninstall() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->getSiteSettingsDirectory() . DIRECTORY_SEPARATOR . 'master.' . $this->getScope() . '.inc';
  }

  /**
   * Return scope.
   *
   * @return string
   *   The scope to execute 'Master' with.
   */
  public function getScope() {
    return $this->getDrupalHelper()->getEnvironment();
  }

  /**
   * Skip check on scope specific variable?
   *
   * @return bool
   *   Whether to skip check on scope specific variable. y default scopes can
   *   only be used if there is at least an empty configuration set.
   */
  public function getSkipScopeValidation() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateName() {
    return '@drupalutils/master.ENVIRONMENT.php.twig';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateVariables() {
    $variables = array(
      'master' => array(
        'modules' => $this->buildModuleList($this->getModules()),
        'baseModules' => $this->buildModuleList($this->getBaseModules()),
        'scope' => $this->getScope(),
        'uninstallBaseBlacklist' => $this->buildModuleList($this->getUninstallBaseBlacklist()),
        'uninstallBlacklist' => $this->buildModuleList($this->getUninstallBlacklist()),
        'version' => $this->getMasterVersion(),
      ),
    );

    return $variables;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return sprintf('Ensure "%s" master modules', $this->getScope());
  }

  /**
   * Return uninstall base blacklist.
   *
   * @return array
   *   The names of modules that should not be uninstalled in all scopes.
   */
  public function getUninstallBaseBlacklist() {
    return array();
  }

  /**
   * Return uninstall blacklist.
   *
   * @return array
   *   The names of modules that should not be uninstalled in the specified scope.
   */
  public function getUninstallBlacklist() {
    return array();
  }

}
