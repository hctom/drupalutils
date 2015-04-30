<?php

/**
 * @file
 * Contains hctom\DrushWrapper\Command\CommandList.
 */

namespace hctom\DrushWrapper\Command;

use hctom\DrushWrapper\Helper\DrushHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Drupal utilities Drush command list class.
 */
class CommandList {

  /**
   * Drush helper.
   *
   * @var DrushHelper
   */
  private $drushHelper;

  /**
   * Constructor.
   *
   * @param DrushHelper $drushHelper
   *   A Drush helper instance object.
   */
  public function __construct(DrushHelper $drushHelper) {
    $this->drushHelper = $drushHelper;
  }

  /**
   * Return Drush helper.
   *
   * @return DrushHelper
   *   The Drush helper object.
   */
  protected function drush() {
    return $this->drushHelper;
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

    $command = new Command($name);

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

    // Build command list (if not already).
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
   * @param Command $command
   *   The command to merge the arguments into.
   * @param $definition
   *   The input definition.
   */
  protected function mergeCommandArguments(Command &$command, $definition) {
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
   * @param Command $command
   *   The command to merge the options into.
   * @param $definition
   *   The input definition.
   */
  protected function mergeCommandOptions(Command $command, $definition) {
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

          $command->addOption(ltrim($optionName, '-'), '', $mode, $description, $defaultValue);
        }
      }
    }
  }

  /**
   * Query all available Drush commands.
   *
   * @return array
   *   An array containing information about all available Drush commands.
   *
   * @throws \RuntimeException
   */
  protected function query() {
    static $result;

    if (!isset($result)) {
      $result = array();

      // Fetch available Drush commands.
      $process = $this->drush()
        ->runProcess('help', array(), array('format' => 'json'), new NullOutput());

      // Unable to parse Drush command definitions?
      if (!$json = json_decode($process->getOutput())) {
        throw new \RuntimeException('Unable to parse Drush command definitions.');
      }

      // Build Drush command list.
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
