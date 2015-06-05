<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Features\DeveloperOrientedViewsSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Views;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task command base class for views related tasks.
 */
class DeveloperOrientedViewsSettingsTask extends ViewsUiTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:views:settings:developer');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    return $this->getDrushProcessHelper()
      ->setCommandName('views-dev')
      ->mustRun('Made views settings more developer-oriented', 'Unable to make views settings more developer-oriented')
      ->getExitCode();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Developer-oriented views settings';
  }

}
