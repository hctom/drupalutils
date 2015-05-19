<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\TaskedCommand.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Task\TaskInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all Drupal Utilities task commands.
 */
abstract class TaskedCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Execute registered tasks.
    return $this->executeTasks($input, $output);
  }

  /**
   * Execute all registered tasks.
   *
   * @param InputInterface $input
   *   The input.
   * @param OutputInterface $output
   *   The output.
   *
   * @return null|int
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function executeTasks(InputInterface $input, OutputInterface $output) {
    // Determine task list.
    if (!($tasks = $this->getTasks())) {
      throw new \RuntimeException(sprintf('No tasks supplied for "%s"', $this->getName()));
    }

    // Create and set up sub-application.
    $subApp = clone $this->getApplication();
    $subApp->setAutoExit(FALSE);
    $subApp->addCommands($tasks);

    // Log table of contents.
    $this->logTableOfContents($tasks);

    // Run tasks.
    $taskCount = 0;
    foreach ($tasks as $task) {
      // Does not implement task interface?
      if (!$task instanceof TaskInterface) {
        throw new \RuntimeException(sprintf('Invalid task class "%s" for "%s"', get_class($task), $task->getName()));
      }

      // Increase task counter.
      $taskCount++;

      // Find task command object.
      $command = $subApp->find($task->getName());

      // Prepare task command arguments.
      $parameters = array(
        'command' => $task->getName(),
      );

      // Output task information.
      $this->getLogger()->always($this->getFormatterHelper()->formatTaskInfo($command, $taskCount, count($tasks)));

      // Build command input.
      $commandInput = new ArrayInput($parameters);
      if ($input->getOption('no-interaction')) {
        $commandInput->setInteractive(FALSE);
      }

      // Run task command and abort, if an error occurred.
      if (($exitCode = $command->run($commandInput, $output))) {
        return $exitCode;
      }
    }
  }

  /**
   * Return tasks.
   *
   * @return TaskInterface[]
   *   An array of task objects.
   */
  public function getTasks() {
    $tasks = array();

    return $tasks;
  }

  /**
   * Log table of contents.
   *
   * @param TaskInterface[] $tasks
   *   All task objects that are about to be executed.
   */
  protected function logTableOfContents(array $tasks) {
    $this->getLogger()->always('');

    $this->getLogger()->always('<label>Tasks to execute ({total}):</label>', array(
      'total' => count($tasks),
    ));

    $this->getLogger()->always('');

    foreach (array_values($tasks) as $i => $task) {
      $this->getLogger()->always('    ' . $this->getFormatterHelper()->formatCounterNumber($i + 1, count($tasks)) . '. ' . $task->getTitle());
    }
  }

}
