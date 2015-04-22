<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\Drush.
 */

namespace hctom\DrupalUtils\Drush;

use hctom\DrupalUtils\Console\Application;
use hctom\DrupalUtils\Process\DrushProcessBuilder;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities Drush class.
 */
class Drush implements DrushSiteAliasAwareInterface {

  use DrushSiteAliasAwareTrait;

  /**
   * Constructor.
   *
   * @param string|null $drushSiteAlias
   *   An optional Drush site alias to use (defaults to '@none').
   */
  public function __construct($drushSiteAlias = null) {
    // Set Drush site alias (if specified).
    if (isset($drushSiteAlias)) {
      $this->setDrushSiteAlias($drushSiteAlias);
    }
  }

  /**
   * Extend input definition with additional arguments/options.
   *
   * @param InputDefinition $definition
   *   The input definition (by reference).
   */
  protected function extendInputDefinition(InputDefinition &$definition) {
    // Add additional options.
    $definition->addOption(new InputOption('debug', null, InputOption::VALUE_NONE, 'Display even more information, including internal messages.'));
    $definition->addOption(new InputOption('no', null, InputOption::VALUE_NONE, "Assume 'no' as answer to all prompts."));
    $definition->addOption(new InputOption('simulate', null, InputOption::VALUE_NONE, "Simulate all relevant actions (don't actually change the system)."));
    $definition->addOption(new InputOption('yes', null, InputOption::VALUE_NONE, "Assume 'yes' as answer to all prompts."));
  }

  /**
   * Return application.
   *
   * @return Application
   *   The application object.
   */
  protected function getApplication() {
    $app = new Application();
    // Set up application.
    $app->setAutoExit(FALSE);
    $app->setName('Drush');
    $app->setVersion($this->getVersion());

    // Extend input definition.
    $inputDefinition = $app->getDefinition();
    $this->extendInputDefinition($inputDefinition);
    $app->setDefinition($inputDefinition);

    // Add Drush commands to application.
    $commandList = new DrushCommandList($this->getDrushSiteAlias());
    $app->addCommands($commandList->getCommands());

    return $app;
  }

  /**
   * Return Drush process builder.
   *
   * @return DrushProcessBuilder
   *   The Drush process builder.
   */
  protected function getProcessBuilder() {
    $processBuilder = new DrushProcessBuilder();

    // Set up process builder.
    $processBuilder
      ->setDrushSiteAlias($this->getDrushSiteAlias());

    return $processBuilder;
  }

  /**
   * Return Drush version.
   *
   * @return string
   *   The Drush version.
   *
   * @throws \Exception
   */
  public function getVersion() {
    static $version;

    if (!isset($version)) {
      $processBuilder = $this->getProcessBuilder();

      // Set up process builder.
      $process = $processBuilder
        ->setArguments(array(
          'command' => 'version',
        ))
        ->setOptions(array(
          'pipe' => TRUE,
        ))
        ->getProcess();

      // Run process.
      $process->run();

      // Error occurred?
      if (!$process->isSuccessful()) {
        throw new \Exception('Unable to determine Drush version.');
      }

      // Parse Drush version.
      $version = $process->getOutput();
    }

    return $version;
  }

  /**
   * Run Drush command.
   *
   * @param InputInterface $input
   *   The input.
   * @param OutputInterface $output
   *   THe output.
   *
   * @return int
   *   0 if everything went fine, or an error code.
   */
  public function run(InputInterface $input, OutputInterface $output) {
    $app = $this->getApplication();

    return $app->run($input, $output);
  }

}
