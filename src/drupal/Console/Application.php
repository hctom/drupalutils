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

    // Merge additional/overriding custom commands from Drush site alias
    // configuration.
    $this->addCustomCommands($input, $output);

    return parent::doRun($input, $output);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultCommands() {
    $defaultCommands = parent::getDefaultCommands();

    // Drush command help.
    $defaultCommands[] = new HelpCommand();

    // Install site.
    $defaultCommands[] = new InstallSiteCommand();

    // List Drush commands.
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

  /**
   * Add custom commands from Drush site alias configuration.
   *
   * @param InputInterface $input
   *   An Input instance.
   * @param OutputInterface $output
   *   An Output instance.
   */
  protected function addCustomCommands(InputInterface $input, OutputInterface $output) {
    // Set up Drush helper.
    $siteAliasDetails = $this->getHelperSet()->get('drush')
      ->setInput($input)
      ->getSiteAliasDetails();

    // Drupal utilities configuration found?
    if (property_exists($siteAliasDetails, 'drupalutils') && property_exists($siteAliasDetails->drupalutils, 'commands')) {
      // Command list is not an array?
      if (!empty($siteAliasDetails->drupalutils->commands) && !is_object($siteAliasDetails->drupalutils->commands)) {
        throw new \RuntimeException('Invalid command configuration found.');
      }

      // Process command list.
      foreach ($siteAliasDetails->drupalutils->commands as $commandName => $class) {
        $output->writeln($commandName . ': ' . $class);
        // Command class exists?
        if (!class_exists($class)) {
          throw new \RuntimeException('Command class not found: ' . $class);
        }

        if ($this->has($commandName)) {
          // TODO Log that an existing ocmmand is overridden.
        }

        // Add custom command.
        $this->add(new $class());
      }
    }
  }

}
