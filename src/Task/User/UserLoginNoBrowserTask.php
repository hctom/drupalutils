<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\User\UserLoginNoBrowserTask.
 */

namespace hctom\DrupalUtils\Task\User;

/**
 * Provides a task command to log in a user without opening a browser.
 */
class UserLoginNoBrowserTask extends UserLoginTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    parent::configure();

    $this
      ->setName('task:user:login:browser:no');
  }

  /**
   * {@inheritdoc}
   */
  public function getBrowser() {
    return 0;
  }

}
