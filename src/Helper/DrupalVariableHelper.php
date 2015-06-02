<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrupalVariableHelper.
 */

namespace hctom\DrupalUtils\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Provides helpers for Drupal variables.
 */
class DrupalVariableHelper extends Helper {

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
   * {@inheritdoc}
   */
  public function getName() {
    return 'drupal_variable';
  }

  /**
   * Return variable value.
   *
   * @param $name
   *   The name of the variable to get the value of.
   *
   * @return mixed
   *   The value of the variable.
   */
  public function getValue($name) {
    $process = $this->getDrushProcessHelper()
      ->setCommandName('variable-get')
      ->setArguments(array(
        'name' => $name,
      ))
      ->setOptions(array(
        'exact' => TRUE,
        'format' => 'var_export',
      ))
      ->run(NULL, NULL, function() {});

    if (($data = @eval('return ' . $process->getOutput() . ';')) === FALSE) {
      throw new RuntimeException(sprintf('Unable to parse variable value of "%s"', $name));
    }

    // Variable exist.
    if (is_array($data) && isset($data[$name])) {
      return $data[$name];
    }

    return NULL;
  }

  /**
   * Set variable value.
   *
   * @param string $name
   *   The name of the variable to set the value for.
   * @param mixed $value
   *   The value to set.
   *
   * @return int|null
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function setValue($name, $value) {
    return $this->getDrushProcessHelper()
      ->setCommandName('variable-set')
      ->setArguments(array(
        'name' => $name,
        'value' => json_encode($value),
      ))
      ->setOptions(array(
        'format' => 'json',
      ))
      ->mustRun()
      ->getExitCode();
  }

}
