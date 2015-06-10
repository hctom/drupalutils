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
   * Feature status: All.
   */
  const FEATURE_STATUS_ALL = 'all';

  /**
   * Feature status: Disabled.
   */
  const FEATURE_STATUS_DISABLED = 'disabled';

  /**
   * Feature status: Enabled.
   */
  const FEATURE_STATUS_ENABLED = 'enabled';

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:features:list');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $process = $this->getDrushProcessHelper()
      ->setCommandName('features-list')
      ->setOptions(array(
        'status' => $this->getFeatureStatus(),
      ))
      ->mustRun(NULL, 'Unable to list features', FALSE);

    $this->getLogger()->always('<info> ' . trim($process->getOutput()) . '</info>');

    return $process->getExitCode();
  }

  /**
   * Return feature status.
   *
   * @return string
   *   The status of the features to list. Possible values:
   *     - static::FEATURE_STATUS_ALL: List all features (default).
   *     - static::FEATURE_STATUS_DISABLED: List disabled features only.
   *     - static::FEATURE_STATUS_ENABLED: List enabled features only.
   */
  public function getFeatureStatus() {
    return static::FEATURE_STATUS_ALL;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'List features';
  }

}
