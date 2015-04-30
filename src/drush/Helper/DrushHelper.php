<?php

/**
 * @file
 * Contains hctom\DrushWrapper\Helper\DrushHelper.
 */

namespace hctom\DrushWrapper\Helper;

use hctom\DrushWrapper\Command\Command;
use hctom\DrushWrapper\Command\CommandList;
use hctom\DrushWrapper\Console\Application;
use Symfony\Component\Console\Helper\InputAwareHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Drupal utilities Drush helper class.
 */
class DrushHelper extends InputAwareHelper {

  /**
   * Drush site alias.
   *
   * @var string
   */
  private $siteAlias;

  /**
   * The output.
   *
   * @var OutputInterface
   */
  private $output;

  /**
   * Return Drush application.
   *
   * @return Application
   *   The Drush application.
   */
  protected function getDrushApplication() {
    $subApp = new Application('DrushWrapper', $this->getVersion());

    $subApp->setAutoExit(FALSE);
    $subApp->addCommands($this->getCommands());

    return $subApp;
  }

  /**
   * Return Drush commands.
   *
   * @return Command[]
   *   An array of command objects.
   */
  protected function getCommands() {
    $commandList = new CommandList($this);

    return $commandList->getCommands();
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drush';
  }

  /**
   * Return output.
   *
   * @return OutputInterface
   *   The output.
   */
  public function getOutput() {
    return $this->output;
  }

  /**
   * Return Drush process.
   *
   * @param string $commandName
   *   The name of the Drush command to execute (the 'drush:' prefix is not
   *   required).
   * @param array $arguments
   *   An optional array of arguments to pass to the Drush process.
   * @param array $options
   *   An optional array of options to pass to the Drush process.
   *
   * @return Process
   *   The Drush process object.
   */
  public function getProcess($commandName, array $arguments = array(), array $options = array()) {
    // Prepare command name.
    $commandName = preg_match('/^drush\:/', $commandName) ? substr($commandName, strlen('drush:')) : $commandName;

    // Add command name to arguments.
    array_unshift($arguments, $commandName);

    // TODO Really needed?
    // Process arguments.
    foreach ($arguments as $argumentName => &$argument) {
      if (empty($argument) || strlen($argument) === 0) {
        unset($arguments[$argumentName]);
      }
    }

    // Define list of skipped options.
    $skippedOptions = array(
      '--ansi',
      '--no-ansi',
      '--site',
      '--verbose',
      '--version',
    );

    // Process options.
    $processedOptions = array();
    foreach ($options as $optionName => &$option) {
      if (!empty($option) || strlen($option) > 0) {
        $optionName = preg_match('/^-{2}/', $optionName)? $optionName : '--' . $optionName;

        // Option is skipped -> continue.
        if (in_array($optionName, $skippedOptions, TRUE)) {
          continue;
        }

        // Option without value?
        if ($option === TRUE) {
          $processedOptions[$optionName] = $optionName;
        }

        // Option with value.
        else {
          $processedOptions[$optionName] = $optionName . '=' . $option;
        }
      }
    }

    // Rewrite '--no-interaction' option to Drush pendant '--yes'.
    if (isset($processedOptions['--no-interaction'])) {
      unset($processedOptions['--no-interaction']);
      unset($processedOptions['-y']);
      $processedOptions['--yes'] = '--yes';
    }

    // Build Drush process.
    $process = $this->getProcessBuilder()
      ->setArguments(array_merge(array_values($processedOptions), array_values($arguments)))
      ->getProcess();

    return $process;
  }

  /**
   * Return Drush process builder.
   *
   * @return ProcessBuilder
   *   The process builder object.
   */
  protected function getProcessBuilder() {
    $processBuilder = new ProcessBuilder();

    return $processBuilder
      ->setPrefix(array(
        'drush',
        $this->getSiteAlias(),
      ));
  }

  /**
   * Return Drush version.
   *
   * @return string
   *   The Drush version.
   */
  protected function getVersion() {
    static $version;

    if (!isset($version)) {
      $version = $this->runProcess('version', array(), array('pipe' => TRUE), new NullOutput())
        ->getOutput();
    }

    return $version;
  }

  /**
   * Return Drush site alias.
   *
   * @return string
   *   The Drush site alias.
   *
   * @throws \RuntimeException
   */
  public function getSiteAlias() {
    if (!$this->siteAlias) {
      throw new \RuntimeException('No Drush site alias has been specified.');
    }

    return $this->siteAlias;
  }

  /**
   * Return Drush site alias details.
   *
   * @return \stdClass
   *   An object containing all details associated to a Drush site alias.
   *
   * @throws \RuntimeException
   */
  public function getSiteAliasDetails() {
    static $details = array();

    $siteAlias = $this->getSiteAlias();
    $siteAliasWithoutAtChar = ltrim($siteAlias, '@');

    if (!isset($details[$siteAlias])) {
      $process = $this->runProcess('site-alias', array('site' => $siteAlias), array('format' => 'json', 'full' => TRUE), new NullOutput());

      // Parse site configuration.
      if (!($details[$siteAlias] = json_decode($process->getOutput()))) {
        throw new \RuntimeException('Unable to parse Drush site alias details: ' . $siteAlias);
      }

      // Does not contain site configuration?
      if (!isset($details[$siteAlias]->{$siteAliasWithoutAtChar})) {
        throw new \RuntimeException('Unable to locate Drush site alias details: ' . $siteAlias);
      }

      // Switch pointer to site configuration.
      $details[$siteAlias] = $details[$siteAlias]->{$siteAliasWithoutAtChar};
    }

    return $details[$siteAlias];
  }

  /**
   * Run Drush command.
   *
   * @param array $parameters
   *   An array of additional parameters.
   * @param InputInterface $input
   *   The base input.
   * @param OutputInterface $output
   *   An optional output (defaults to the application's output).
   *
   * @return int
   *   0 if everything went fine, or an error code
   */
  public function runCommand(array $parameters, InputInterface $input, OutputInterface $output = NULL) {
    // Inject site alias (if not set already).
    if (empty($parameters['--site'])) {
      $parameters['--site'] = $this->getSiteAlias();
    }

    // Inject options of parent application.
    foreach ($input->getOptions() as $optionName => $option) {
      if (!empty($option) || strlen($option) > 0) {
        $parameters['--' . $optionName] = $option;
      }
    }

    return $this->getDrushApplication()
      ->run(new ArrayInput($parameters), isset($output) ? $output : $this->getOutput());
  }

  /**
   * Run Drush process.
   *
   * @param string $commandName
   *   The name of the Drush command to execute (the 'drush:' prefix is not
   *   required).
   * @param array $arguments
   *   An optional array of arguments to pass to the Drush process.
   * @param array $options
   *   An optional array of options to pass to the Drush process.
   * @param OutputInterface $output
   *   An optional output (defaults to the application's output).
   *
   * @return Process
   *   The Drush process object.
   */
  public function runProcess($commandName, array $arguments = array(), array $options = array(), OutputInterface $output = NULL) {
    $output = isset($output) ? $output : $this->getOutput();

    // Build Drush process.
    $process = $this->getProcess($commandName, $arguments, $options);

    // Debug: Output Drush command.
    if (isset($output)) {
      /*if ($output->isDebug()) {
        // TODO Implement a logger for this?
        $output->writeln('<comment>Drush:</comment> ' . $process->getCommandLine());
      }*/
    }

    // Run process and write output (if any).
    $process->run(function($type, $buffer) use ($output) {
      if (isset($output)) {
        $output->write($buffer);
      }
    });

    // Error occurred?
    if (!$process->isSuccessful()) {
      throw new \RuntimeException('Unable to execute Drush command:' . "\n" . $process->getCommandLine());
    }

    return $process;
  }

  /**
   * {@inheritdoc}
   */
  public function setInput(InputInterface $input) {
    parent::setInput($input);

    $siteAlias = $siteAliasDefault = '@none';

    // Inspect input for Drush site alias.
    if ($input->hasOption('site')) {
      $siteAlias = $input->getOption('site');
    }
    elseif ($input->hasParameterOption('--site')) {
      $siteAlias = $input->getParameterOption('--site', $siteAliasDefault);
    }

    // Ensure Drush site alias.
    $siteAlias = trim($siteAlias) ? trim($siteAlias) : $siteAliasDefault;

    // Assign Drush site alias.
    $this->setSiteAlias($siteAlias);

    return $this;
  }

  /**
   * Set output.
   *
   * @param OutputInterface $output
   *   An OutputInterface instance.
   *
   * @return $this
   *   A self-reference for method chaining.
   */
  public function setOutput(OutputInterface $output) {
    $this->output = $output;

    return $this;
  }

  /**
   * Set Drush site alias.
   *
   * @param string $siteAlias
   *   The Drush site alias.
   *
   * @return $this
   *   A self-reference for method chaining.
   *
   * @see getSiteAliasDetails()
   *
   * @throws \Exception
   */
  public function setSiteAlias($siteAlias) {
    $this->siteAlias = '@' . ltrim($siteAlias, '@');

    // Validate Drush site alias.
    $this->getSiteAliasDetails();

    return $this;
  }

}
