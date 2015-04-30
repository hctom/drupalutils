<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Site\InstallSiteTask.
 */

namespace hctom\DrupalUtils\Task\Site;

use hctom\DrupalUtils\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities task class: Install site.
 */
class InstallSiteTask extends Task {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:site:install');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    return $this->drush()
      ->runCommand(array(
        'command' => 'drush:site-install',
        'profile' => $this->getInstallProfile(),
        '--account-mail' => $this->getAccountMail(),
        '--account-name' => $this->getAccountName(),
        '--account-pass' => $this->getAccountPassword(),
        '--clean-url' => $this->getCleanUrlEnabled(),
        '--locale' => $this->getLocale(),
        '--site-mail' => $this->getSiteMail(),
        '--site-name' => $this->getSiteName(),
        '--sites-subdir' => $this->getSiteDirectoryName(),
        '--yes' => TRUE,
      ), $input);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Install Drupal site';
  }

  /**
   * Return admin user e-mail address.
   *
   * @return string|null
   *   The e-mail address to use for the admin user (defaults to
   *   'admin@example.com').
   */
  public function getAccountMail() {
    return NULL;
  }

  /**
   * Return admin user name.
   *
   * @return string|null
   *   The username to use for the admin user (defaults to 'admin')
   */
  public function getAccountName() {
    return NULL;
  }

  /**
   * Return admin user password.
   *
   * @return string|null
   *   The password to use for the admin user (defaults to 'pass', return NULL
   *   to generate a random password).
   */
  public function getAccountPassword() {
    return 'pass';
  }

  /**
   * Enable clean URLs?
   *
   * @return bool|null
   *   Whether clean URLs should be enabled or not (defaults to TRUE).
   */
  public function getCleanUrlEnabled() {
    return NULL;
  }

  /**
   * Return install profile name.
   *
   * @return string|null
   *   The name of the profile to install (defaults to 'standard').
   */
  public function getInstallProfile() {
    return NULL;
  }

  /**
   * Return language code.
   *
   * @return string|null
   *   The language code to use (defaults to 'en').
   */
  public function getLocale() {
    return NULL;
  }

  /**
   * Return site directory name.
   *
   * @return string|null
   *   The name of directory under 'sites' which should be created (defaults to
   *   'default').
   */
  public function getSiteDirectoryName() {
    return NULL;
  }

  /**
   * Return site e-mail address.
   *
   * @return string|null
   *   The e-mail address to use for sending e-mail from the site (defaults to
   *   'admin@example.com').
   */
  public function getSiteMail() {
    return NULL;
  }

  /**
   * Return site name.
   *
   * @return string|null
   *   The site name to use (defaults to 'Site-Install').
   */
  public function getSiteName() {
    return NULL;
  }

}
