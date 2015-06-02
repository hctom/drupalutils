<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\ListFeaturesTask.
 */

namespace hctom\DrupalUtils\Task\Features;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to list all features and their status.
 */
class ListFeaturesTask extends FeaturesTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:features:list');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $process = $this->getDrushProcessHelper()
      ->setCommandName('features-list')
      ->mustRun(NULL, 'Unable to list features', FALSE);

    $this->getLogger()->always('<info> ' . trim($process->getOutput()) . '</info>');

    return $process->getExitCode();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'List features';
  }

}
