<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Master\EnsureLocalMasterModulesTask.
 */

namespace hctom\DrupalUtils\Task\Master;

/**
 * Provides a task command to ensure default local master modules.
 */
class EnsureLocalMasterModulesTask extends EnsureMasterModulesTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:site:master:ensure:modules:local');
  }

  /**
   * {@inheritdoc}
   */
  public function getModules() {
    return array_merge(parent::getModules(), array(
      'devel',
      'diff',
      'field_ui',
      'stage_file_proxy',
      'update',
      'views_ui',
    ));
  }

}
