<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\DrushCommandList.
 */

namespace hctom\DrupalUtils\Drush;

use hctom\DrupalUtils\Process\DrushProcessBuilder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Drupal utilities Drush command list class.
 */
class DrushCommandList implements DrushSiteAliasAwareInterface {

  use DrushSiteAliasAwareTrait;

  /**
   * Constructor.
   *
   * @param string|null $drushSiteAlias
   *   An optional Drush site alias to use (defaults to '@none').
   */
  public function __construct($drushSiteAlias = null) {
    // Set Drush site alias (if specified).
    if (isset($siteAlias)) {
      $this->setDrushSiteAlias($drushSiteAlias);
    }
  }

  /**
   * Return command.
   *
   * @param string $name
   *   The name of the command.
   * @param \stdClass $definition
   *   The command definition.
   * @return Command
   *   The command object.
   */
  protected function getCommand($name, $definition) {
    $name = 'drush:' . $name;

    $command = new DrushCommand($name);

    // Set up command.
    $command
      ->setDescription($definition->description)
      ->setHelp($definition->description);

    // Merge Drush command arguments.
    $this->mergeCommandArguments($command, $definition);

    // Merge Drush command options.
    $this->mergeCommandOptions($command, $definition);

    return $command;
  }

  /**
   * Return Drush commands.
   *
   * @return Command[]
   *   An array of Drush command objects.
   */
  public function getCommands() {
    static $commands;

    if (!isset($commands)) {
      foreach ($this->query() as $commandName => $commandDefinition) {
        $command = $this->getCommand($commandName, $commandDefinition);
        $commands[] = $command;
      }
    }

    return $commands;
  }

  /**
   * Merge command arguments.
   *
   * @param DrushCommand $command
   *   The command to merge the arguments into.
   * @param $definition
   *   The input definition.
   */
  protected function mergeCommandArguments(DrushCommand &$command, $definition) {
    if (!empty($definition->arguments)) {
      $argumentCount = 0;
      $numRequiredArguments = $definition->{'required-arguments'};

      foreach ($definition->arguments as $argumentName => $argument) {
        if ($argumentCount < $numRequiredArguments) {
          $mode = InputArgument::REQUIRED;
        }
        else {
          $mode = InputArgument::OPTIONAL;
        }

        // TODO Try to figure out, if array mode has to be applied.

        // Add argument to command.
        $command->addArgument($argumentName, $mode, $argument);

        $argumentCount++;
      }
    }
  }

  /**
   * Merge command options.
   *
   * @param DrushCommand $command
   *   The command to merge the options into.
   * @param $definition
   *   The input definition.
   */
  protected function mergeCommandOptions(DrushCommand $command, $definition) {
    if (!empty($definition->options)) {
      foreach ($definition->options as $optionName => $option) {
        if (empty($option->hidden)) {
          $mode = InputOption::VALUE_OPTIONAL;

          // TODO Try to figure out correct option mode.
          if (!empty($option->list)) {
            $mode |= InputOption::VALUE_IS_ARRAY;
          }

          // Description.
          if (is_string($option)) {
            $description = $option;
          }
          else {
            $description = $option->description;
          }

          // Default value.
          if (isset($option->default)) {
            $defaultValue = $option->default;
          }
          else {
            $defaultValue = NULL;
          }

          $command->addOption($optionName, '', $mode, $description, $defaultValue);
        }
      }
    }
  }

  /**
   * Query all available Drush commands.
   *
   * @return array
   *   An array conaining information about all available Drush commands.
   *
   * @throws \Exception
   */
  protected function query() {
    static $result;

    if (!isset($result)) {
      $result = array();

      $processBuilder = new DrushProcessBuilder();

      $process = $processBuilder
        ->setDrushSiteAlias($this->getDrushSiteAlias())
        ->setArguments(array(
          'command' => 'help',
        ))
        ->setOptions(array(
          '--format' => 'json'
        ))
        ->getProcess();

      $process->run();

      if (!$process->isSuccessful()) {
        throw new \Exception('Unable to load Drush command definitions.');
      }

      if (!$json = json_decode($process->getOutput())) {
        throw new \Exception('Unable to parse Drush command definitions.');
      }

      foreach ($json as $groupName => $group) {
        if (!empty($group->commands)) {
          foreach ($group->commands as $commandName => $commandDefinition) {
            $result[$commandName] = $commandDefinition;
          }
        }
      }
    }

    return $result;
  }

}
