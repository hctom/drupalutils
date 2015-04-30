<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Console\Application.
 */

namespace hctom\DrupalUtils\Console;

use hctom\DrupalUtils\Command\Drush\HelpCommand;
use hctom\DrupalUtils\Command\Drush\ListCommand;
use hctom\DrupalUtils\Command\Site\InstallSiteCommand;
use hctom\DrupalUtils\Helper\DrupalHelper;
use hctom\DrushWrapper\Helper\DrushHelper;
use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities application class.
 */
class Application extends SymfonyConsoleApplication {

  /**
   * {@inheritdoc}
   */
  public function doRun(InputInterface $input, OutputInterface $output) {
    // Assign output to Drush helper.
    $this->getHelperSet()->get('drush')
      ->setOutput($output);

    // TODO Other commands from Drush site alias config.

    return parent::doRun($input, $output);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultCommands() {
    $defaultCommands = parent::getDefaultCommands();

    $defaultCommands[] = new HelpCommand();
    $defaultCommands[] = new InstallSiteCommand();
    $defaultCommands[] = new ListCommand();

    return $defaultCommands;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultHelperSet() {
    $helperSet = parent::getDefaultHelperSet();

    // Drupal helper.
    $helperSet->set(new DrupalHelper());

    // Drush helper.
    $helperSet->set(new DrushHelper());

    return $helperSet;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultInputDefinition() {
    $inputDefinition = parent::getDefaultInputDefinition();

    // Drush site alias option.
    $inputDefinition->addOptions(array(
      new InputOption('simulate', NULL, InputOption::VALUE_NONE, "Simulate all relevant actions (don't actually change the system)."),
      new InputOption('site', NULL, InputOption::VALUE_REQUIRED, 'The Drush site alias to use.', '@none'),
    ));

    return $inputDefinition;
  }

}
