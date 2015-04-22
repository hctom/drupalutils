<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Console\Application.
 */

namespace hctom\DrupalUtils\Console;

use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Input\InputOption;

/**
 * Drupal utilities application class.
 */
class Application extends SymfonyConsoleApplication {

  /**
   * Input option name: Drush site alias.
   */
  const INPUT_OPTION_DRUSH_SITE_ALIAS = 'drush-site-alias';

  /**
   * {@inheritdoc}
   */
  protected function getDefaultInputDefinition() {
    $inputDefinition = parent::getDefaultInputDefinition();

    // Additional options.
    $inputDefinition->addOptions(array(
      new InputOption(Application::INPUT_OPTION_DRUSH_SITE_ALIAS, NULL, InputOption::VALUE_REQUIRED, 'The Drush site alias to use.', '@none'),
    ));

    return $inputDefinition;
  }

}
