<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Command\TaskedCommand.
 */

namespace hctom\DrupalUtils\Command;

use hctom\DrupalUtils\Console\Application;
use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities tasked command base class.
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
   *   An InputInterface instance.
   * @param OutputInterface $output
   *   An OutputInterface instance.
   *
   * @return null|int
   *   NULL or 0 if everything went fine, or an error code.
   */
  public function executeTasks(InputInterface $input, OutputInterface $output) {
    $taskCount = 0;

    // Determine task list.
    $tasks = $this->getTasks();

    // Set up sub-application.
    $subApp = new Application();
    $subApp->setAutoExit(FALSE);
    $subApp->addCommands($tasks);

    // Execute tasks.
    foreach ($tasks as $task) {
      $subInput = array(
        'command' => $task->getName(),
      );

      // TODO Better method of passing global options to sub-application.
      foreach ($input->getOptions() as $optionName => $option) {
        if (!empty($option) || strlen($option) > 0) {
          $subInput['--' . ltrim($optionName, '-')] = $option;
        }
      }
      // FIXME Verbosity get's lost during executions.
      $subInput['--verbose'] = $output->getVerbosity();

      // Increase task counter.
      $taskCount++;

      // Output task counter/title.
      $terminalDimensions = $this->getApplication()->getTerminalDimensions();
      $taskCountFormat = '%0' . strlen(count($tasks)) . 'd';
      $taskCounter = sprintf($taskCountFormat, $taskCount) . '/' . sprintf($taskCountFormat, count($tasks));
      $taskTitle = '<comment>' . $task->getTitle() . '</comment>';
      $output->writeln('');
      $output->writeln(str_pad($this->formatter()->formatSection($taskCounter, $taskTitle) . ' ', $terminalDimensions[0], '>'));
      $output->writeln('');

      // Run task and abort, if an error occurred.
      if (($exitCode = $subApp->run(new ArrayInput($subInput), $output))) {
        return $exitCode;
      }
    }
  }

  /**
   * Return tasks (including default tasks).
   *
   * @return Task[]
   *   An array of task objects.
   */
  public function getTasks() {
    $tasks = array();

    return $tasks;
  }

}
