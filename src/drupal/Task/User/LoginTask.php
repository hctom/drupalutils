<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\User\LoginTask.
 */

namespace hctom\DrupalUtils\Task\User;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities task class: Log in user.
 */
class LoginTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:user:login');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    return $this->drush()
      ->runCommand(array(
        'command' => 'drush:user-login',
        'user' => $this->getUser(),
        'path' => $this->getRedirectPath(),
        '--browser' => $this->getBrowser(),
        '--redirect-port' => $this->getRedirectPort(),
      ), $input);
  }

  /**
   * Return browser.
   *
   * @return string|int|null
   *   The browser to use (defaults to operating system default). Return 0 to
   *   suppress opening a browser.
   */
  public function getBrowser() {
    return NULL;
  }

  /**
   * Return redirect path.
   *
   * @return string|null
   *   The path to redirect to after logging in (defaults to the user profile
   *   page of the logged in user).
   */
  public function getRedirectPath() {
    return NULL;
  }

  /**
   * Return redirect port.
   *
   * @return string|null
   *   The custom port for redirecting to (e.g. when running within a Vagrant
   *   environment).
   */
  public function getRedirectPort() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Log in user';
  }

  /**
   * Return user.
   *
   * @return string|int|null
   *   The user ID, user name, or e-mail address for the user to log in as
   *   (defaults to User ID: 1).
   */
  public function getUser() {
    return NULL;
  }

}
