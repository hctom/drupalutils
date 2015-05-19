<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\SiteAliasConfig.
 */

namespace hctom\DrupalUtils\Drush;

/**
 * Provides a typed object of the configuration for the Drush site alias.
 */
class SiteAliasConfig {

  /**
   * The configuration data.
   *
   * @var \stdClass
   */
  private $config;

  /**
   * Constructor.
   *
   * @param \stdClass $config
   *   The configuration data.
   */
  public function __construct(\stdClass $config) {
    $this->config = $config;
  }

  /**
   * Return class loader namespaces.
   *
   * @return \stdClass
   *   The configured class loader namespaces.
   */
  public function getClassLoaderNamespaces() {
    $drupalUtilsConfig = $this->getDrupalUtils();

    if ($drupalUtilsConfig && property_exists($drupalUtilsConfig, 'autoload')) {
      // Is not an associative array?
      if (!empty($drupalUtilsConfig->autoload) && !is_object($drupalUtilsConfig->autoload)) {
        throw new \RuntimeException('Invalid class loader configuration');
      }

      return $drupalUtilsConfig->autoload;
    }
  }

  /**
   * Return PSR-4 class loader namespaces.
   *
   * @return \stdClass
   *   The configured PSR-4 class loader namespaces.
   */
  public function getPsr4ClassLoaderNamespaces() {
    if (!($classLoaderNamespaces = $this->getClassLoaderNamespaces()) || !property_exists($classLoaderNamespaces, 'psr-4')) {
      return;
    }

    // Is not an associative array?
    elseif (!is_object($classLoaderNamespaces->{'psr-4'})) {
      throw new \RuntimeException('Invalid PSR-4 class loader configuration');
    }

    return $classLoaderNamespaces->{'psr-4'};
  }

  public function getCommands() {
    $drupalUtilsConfig = $this->getDrupalUtils();

    if ($drupalUtilsConfig && property_exists($drupalUtilsConfig, 'commands')) {
      // Is not an array?
      if (!empty($drupalUtilsConfig->commands) && !is_array($drupalUtilsConfig->commands)) {
        throw new \RuntimeException('Invalid command configuration');
      }

      return $drupalUtilsConfig->commands;
    }
  }

  /**
   * Return configuration.
   *
   * @return \stdClass
   *   The full configuration data.
   */
  public function getConfig() {
    return $this->config;
  }

  public function getDrupalUtils() {
    if (property_exists($this->getConfig(), 'drupalutils')) {
      return $this->getConfig()->drupalutils;
    }
  }

  /**
   * Return environment indicator.
   *
   * @return string
   *   The environment indicator.
   *
   * @throws \RuntimeException
   */
  public function getEnvironment() {
    $defaultEnvironment = 'live';

    // No Drupal Utilities configuration -> fall back to default environment.
    if (!($drupalUtilsConfig = $this->getDrupalUtils())) {
      return $defaultEnvironment;
    }

    // No environment indicator set -> fall back to default environment.
    elseif (!property_exists($drupalUtilsConfig, 'environment') || empty($drupalUtilsConfig->environment)) {
      return $defaultEnvironment;
    }

    return $drupalUtilsConfig->environment;
  }

  /**
   * Return Drupal's URI host.
   *
   * @return string
   *   The host name of the Drupal site URI.
   *
   * @throws \RuntimeException
   */
  public function getHostName() {
    $config = $this->getConfig();

    if (!property_exists($config, 'uri')) {
      throw new \RuntimeException("Unable to determine Drupal's URI host name");
    }

    return parse_url($config->uri, PHP_URL_HOST) ? parse_url($config->uri, PHP_URL_HOST) : $config->uri;
  }

  /**
   * Return Drupal's root path.
   *
   * @return string
   *   The absolute path of the Drupal document root.
   *
   * @throws \RuntimeException
   */
  public function getRootPath() {
    $config = $this->getConfig();

    if (!property_exists($config, 'root')) {
      throw new \RuntimeException("Unable to determine Drupal's document root path");
    }

    return $config->root;
  }

}
