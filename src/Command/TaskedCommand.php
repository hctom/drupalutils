<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\TaskedCommand.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Collection\DataCollectionInterface;
use hctom\DrupalUtils\Task\TaskInterface;
use hctom\DrupalUtils\Task\TaskList;
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
   * @var DataCollectionInterface
   */
  private $taskList;

  /**
   * Initialize task list.
   */
  protected function doInitializeTaskList() {

  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Initialize task list.
    $this->doInitializeTaskList();

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
    $taskList = $this->getTaskList();

    // Task list is empty.
    if ($taskList->isEmpty()) {
      throw new \RuntimeException(sprintf('No tasks supplied for "%s"', $this->getName()));
    }


    // Create and set up sub-application.
    $subApp = clone $this->getApplication();
    $subApp->setAutoExit(FALSE);
    $subApp->addCommands($taskList->toArray());

    // Log table of contents.
    $this->logTableOfContents();

    // Prompt if tasks should be executed.
    $defaultValue = 'yes';
    $question = new ConfirmationQuestion($formatter->formatQuestion('Continue', $defaultValue), $defaultValue);
    $continue = $this->getQuestionHelper()->ask($input, $output, $question);

    if ($continue) {
      /* @var TaskInterface $task */
      foreach ($taskList->getValues() as $i => $task) {
        // Find task command object.
        $command = $subApp->find($task->getName());

        // Prepare task command arguments.
        $parameters = array(
          'command' => $task->getName(),
        );

        $this->getLogger()->always('');
        $this->getLogger()->always($formatter->formatDivider());

        // Output task information.
        $this->getLogger()->always($formatter->formatTaskInfo($command, $i + 1, $taskList->count()));

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
   * Return task list.
   *
   * @return DataCollectionInterface
   *   A task list collection object.
   */
  protected function getTaskList() {
    if (!isset($this->taskList)) {
      $this->taskList = new TaskList();
    }

    return $this->taskList;
  }

  /**
   * Log table of contents.
   */
  protected function logTableOfContents() {
    $taskList = $this->getTaskList();
    $formatter = $this->getFormatterHelper();

    $this->getLogger()->always('');

    $toc = array();
    $toc[] = sprintf('Tasks to execute (%s):', $taskList->count());
    $toc[] = '';

    /* @var TaskInterface $task */
    foreach ($taskList->getValues() as $i => $task) {
      $toc[] = '  ' . $formatter->formatCounterNumber($i + 1, $taskList->count()) . '. ' . $task->getTitle();
    }

    $this->getLogger()->always($formatter->formatBlock($toc, 'toc', TRUE));
  }

}
