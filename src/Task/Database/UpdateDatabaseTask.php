<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Database\UpdateDatabaseTask.
 */

namespace hctom\DrupalUtils\Task\Database;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to apply any database updates required (as with
 * running update.php).
 */
class UpdateDatabaseTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:database:update');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    return $this->getDrushProcessHelper()
      ->setCommandName('updatedb')
      ->mustRun('Applied database updates', 'Unable to apply database updates');
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Apply database updates';
  }

  /**
   * {@inheritdoc}
   */
  protected function skipWithMessage() {
    if (($message = parent::skipWithMessage())) {
      return $message;
    }

    $process = $this->getDrushProcessHelper()
      ->setCommandName('updatedb-status')
      ->setOptions(array(
        'format' => 'json'
      ))
      ->mustRun(NULL, NULL, FALSE);

    if (json_decode($process->getOutput()) === NULL) {
      return 'No database update(s) to apply';
    }
  }


}
