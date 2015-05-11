<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\ProcessHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Output\OutputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * The ProcessHelper class provides helpers to run external processes.
 */
class ProcessHelper extends Helper implements LoggerAwareInterface, OutputAwareInterface {

  use LoggerAwareTrait;
  use OutputAwareTrait; // TODO Use a logger only.

  /**
   * The name of the command to run.
   *
   * @var string
   */
  private $commandName;

  /**
   * The arguments of the process.
   *
   * @var array
   */
  private $arguments;

  /**
   * The options of the process.
   *
   * @var array
   */
  private $options;

  /**
   * Build process arguments.
   *
   * @return array
   *   The processed arguments.
   */
  protected function buildArguments() {
    return array_values($this->getArguments());
  }

  /**
   * Build process options.
   *
   * @return array
   *   The processed options.
   */
  protected function buildOptions() {
    $options = array();

    foreach ($this->getOptions() as $optionName => $option) {
      // Option without value?
      if ($option === TRUE) {
        $options[$optionName] = '--' . $optionName;
      }

      // Option with value.
      else {
        $options[$optionName] = '--' . $optionName . '=' . $option;
      }
    }

    return $options;
  }

  /**
   * Return escaped string.
   */
  protected function escapeString($string) {
    return str_replace('<', '\\<', $string);
  }

  /**
   * Return arguments.
   *
   * @return array
   *   The arguments to pass to the process.
   */
  public function getArguments() {
    return $this->arguments ? $this->arguments : array();
  }

  /**
   * Return command name.
   *
   * @return array
   *   The name of the command to run.
   */
  public function getCommandName() {
    if (!$this->commandName) {
      throw new LogicException('No command name has been set');
    }

    return $this->commandName;
  }

  /**
   * Return logger.
   *
   * @return LoggerInterface
   *   The logger.
   */
  public function getLogger() {
    return $this->logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'process';
  }

  /**
   * Return options.
   *
   * @return array
   *   The options to pass to the process.
   */
  public function getOptions() {
    return $this->options ? $this->options : array();
  }

  /**
   * Return process.
   *
   * @return Process
   *   Th process object.
   */
  public function getProcess() {
    $processBuilder = $this->getProcessBuilder();
    $process = $processBuilder->getProcess();

    return $process;
  }

  /**
   * Return process builder.
   *
   * @return ProcessBuilder
   *   The process builder object.
   */
  protected function getProcessBuilder() {
    // Build parameters.
    $parameters = array_merge($this->buildOptions(), $this->buildArguments());

    // Initialize process builder.
    $processBuilder = ProcessBuilder::create($parameters);
    $processBuilder->setPrefix(array(
      $this->getCommandName(),
    ));

    return $processBuilder;
  }

  /**
   * Run process.
   *
   * This is identical to run() except that an exception is thrown if the process
   * exits with a non-zero exit code.
   *
   * @param string|null $successMessage
   *   An optional message to display on success.
   * @param string|null $errorMessage
   *   An optional message to display on error.
   * @param callable|null $callback
   *   A PHP callback to run whenever there is some output available on STDOUT
   *   or STDERR.
   *
   * @return Process
   *   The process that ran.
   */
  public function mustRun($successMessage = NULL, $errorMessage = NULL, $callback = NULL) {
    $process = $this->run($successMessage, $errorMessage, $callback);

    if (!$process->isSuccessful()) {
      throw new RuntimeException('Unable to run process: ' . $process->getCommandLine());
    }

    return $process;
  }

  /**
   * Reset command name, arguments and options.
   *
   * @return static
   *    self-reference for method chaining.
   */
  public function reset() {
    return $this->setCommandName(NULL)
      ->setArguments(array())
      ->setOptions(array());
  }

  /**
   * Run process.
   *
   * @param string|null $successMessage
   *   An optional message to display on success.
   * @param string|null $errorMessage
   *   An optional message to display on error.
   * @param callable|null $callback
   *   A PHP callback to run whenever there is some output available on STDOUT
   *   or STDERR.
   *
   * @return Process
   *   The process that ran.
   */
  public function run($successMessage = NULL, $errorMessage = NULL, $callback = NULL) {
    $process = $this->getProcess();

    // Log process.
    $this->getLogger()->debug('Starting process: <code>{commandLine}</code>', array(
      'commandLine' => $this->escapeString($process->getCommandLine()),
    ));

    $process->run(function($type, $buffer) use ($callback) {
      $processOutputPrefix = '<bg=' . ($type === Process::ERR ? 'red' : 'white') . '> </>  ';

      // Display output?
      if ($callback !== FALSE) {
        // Custom callback?
        if ($callback !== NULL) {
          call_user_func($callback, $type, $buffer);
        }

        // Default callback (if verbose).
        elseif ($this->getOutput()->isVerbose()) {
          // TODO Use a logger.
          $buffer = $processOutputPrefix . str_replace("\n", "\n" . $processOutputPrefix, trim($buffer));
          $this->getOutput()->writeln($buffer);
        }
      }
    });

    // Successful -> Display success message (if any).
    if ($process->isSuccessful() && $successMessage !== NULL) {
      // TODO Use a logger.
      $this->getOutput()->writeln(sprintf('<bg=green> </>  <info>%s</info>', $successMessage));
    }

    // Not successful -> Display error message (if any).
    elseif (!$process->isSuccessful() && $errorMessage !== NULL) {
      // TODO Use a logger.
      $this->getOutput()->writeln(sprintf('<error> %s </error>', $errorMessage));
    }

    return $process;
  }

  /**
   * Set process arguments.
   *
   * @param array $arguments
   *   The arguments to pass to the process.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setArguments(array $arguments) {
    $this->arguments = $arguments;

    return $this;
  }

  /**
   * Set command name.
   *
   * @param string $commandName
   *   The name of the command to run.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setCommandName($commandName) {
    $this->commandName = $commandName;

    return $this;
  }

  /**
   * Set process options.
   *
   * @param array $options
   *   The options to pass to the process.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setOptions(array $options) {
    $this->options = $options;

    return $this;
  }

}
