<?php

/**
 * @file
 * Contains hctom\DrushWrapper\Console\Application.
 */

namespace hctom\DrushWrapper\Console;

use hctom\DrushWrapper\Helper\DrushHelper;
use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities Drush wrapper application class.
 */
class Application extends SymfonyConsoleApplication {

  /**
   * {@inheritdoc}
   */
  public function doRun(InputInterface $input, OutputInterface $output) {
    // Assign output to Drush helper.
    $this->getHelperSet()->get('drush')
      ->setOutput($output);

    return parent::doRun($input, $output);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultHelperSet() {
    $helperSet = parent::getDefaultHelperSet();

    // Drush helper.
    $helperSet->set(new DrushHelper());

    return $helperSet;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultInputDefinition() {
    $inputDefinition = parent::getDefaultInputDefinition();

    // Additional options.
    $inputDefinition->addOptions(array(
      new InputOption('--debug', NULL, InputOption::VALUE_NONE, 'Display even more information, including internal messages.'),
      new InputOption('--no', NULL, InputOption::VALUE_NONE, "Assume 'no' as answer to all prompts."),
      new InputOption('--simulate', NULL, InputOption::VALUE_NONE, "Simulate all relevant actions (don't actually change the system)."),
      new InputOption('--site', NULL, InputOption::VALUE_REQUIRED, 'The Drush site alias to use.', '@none'),
      new InputOption('--yes', NULL, InputOption::VALUE_NONE, "Assume 'yes' as answer to all prompts."),
    ));

    return $inputDefinition;
  }

}
