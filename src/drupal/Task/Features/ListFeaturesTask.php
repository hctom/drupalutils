<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\ListFeaturesTask.
 */

namespace hctom\DrupalUtils\Task\Features;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities task class: List features.
 */
class ListFeaturesTask extends Task {

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
    return $this->drush()
      ->runCommand(array(
        'command' => 'drush:features-list',
      ), $input);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'List features';
  }

}
