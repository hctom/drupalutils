<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Site\CoreStatusTask.
 */

namespace hctom\DrupalUtils\Task\Site;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to display Drupal core status information.
 */
class CoreStatusTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:site:status');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $process = $this->getDrushProcessHelper()
      ->setCommandName('core-status')
      ->setOptions(array(
        'full' => $this->getShowFullStatus(),
        'show-passwords' => $this->getShowPasswords(),
      ))
      ->mustRun(NULL, 'Unable to fetch Drupal core status information', FALSE);

    $this->getLogger()->always('<info> ' . trim($process->getOutput()) . '</info>');

    return $process->getExitCode();
  }

  /**
   * Show full status information?
   *
   * @return bool
   *   Whether to show all status information.
   */
  public function getShowFullStatus() {
    return TRUE;
  }

  /**
   * Show passwords?
   *
   * @return bool
   *   Whether to show passwords.
   */
  public function getShowPasswords() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Drupal core status';
  }

}
