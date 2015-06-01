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
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Base class for all Drupal Utilities task commands.
 */
abstract class TaskedCommand extends Command {

  /**
   * Task list.
   *
   * @var array
   */
  private $taskList = array();

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
   * @return int|null
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function executeTasks(InputInterface $input, OutputInterface $output) {
    $formatter = $this->getFormatterHelper();

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

    // Prompt if tasks should be executed.
    $defaultValue = 'yes';
    $question = new ConfirmationQuestion($formatter->formatQuestion('Continue', $defaultValue), $defaultValue);
    $continue = $this->getQuestionHelper()->ask($input, $output, $question);

    if ($continue) {
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

        $this->getLogger()->always('');
        $this->getLogger()->always($formatter->formatDivider());

        // Output task information.
        $this->getLogger()->always($formatter->formatTaskInfo($command, $taskCount, count($tasks)));

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
  }

  /**
   * Return tasks.
   *
   * @return TaskInterface[]
   *   An array of task objects.
   */
  public function getTasks() {
    return $this->taskList;
  }

  /**
   * Log table of contents.
   *
   * @param TaskInterface[] $tasks
   *   All task objects that are about to be executed.
   */
  protected function logTableOfContents(array $tasks) {
    $formatter = $this->getFormatterHelper();

    $this->getLogger()->always('');

    $toc = array();
    $toc[] = sprintf('Tasks to execute (%s):', count($tasks));
    $toc[] = '';

    foreach (array_values($tasks) as $i => $task) {
      $toc[] = '  ' . $formatter->formatCounterNumber($i + 1, count($tasks)) . '. ' . $task->getTitle();
    }

    $this->getLogger()->always($formatter->formatBlock($toc, 'toc', TRUE));
  }

  /**
   * Register task.
   *
   * @param TaskInterface $task
   *   A task object to register.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function registerTask(TaskInterface $task) {
    $this->taskList[$task->getName()] = $task;

    return $this;
  }

  /**
   * Register tasks.
   *
   * @param TaskInterface[] $tasks
   *   An array of task objects to register.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function registerTasks(array $tasks) {
    foreach ($tasks as $task) {
      $this->registerTask($task);
    }

    return $this;
  }

  /**
   * Remove task.
   *
   * @param string $taskName
   *   A task name.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function removeTask($taskName) {
    if (isset($this->taskList[$taskName])) {
      unset($this->taskList[$taskName]);
    }

    return $this;
  }

  /**
   * Remove tasks.
   *
   * @param array $taskNames
   *   An array of task names.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function removeTasks(array $taskNames) {
    foreach ($taskNames as $taskName) {
      $this->removeTask($taskName);
    }

    return $this;
  }

}
