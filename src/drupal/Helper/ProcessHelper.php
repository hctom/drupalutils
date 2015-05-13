<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\ProcessHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Log\LoggerInterface;
use hctom\DrupalUtils\Output\OutputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * The ProcessHelper class provides helpers to run external processes.
 */
class ProcessHelper extends Helper implements LoggerAwareInterface, OutputAwareInterface {

  use OutputAwareTrait;
  use LoggerAwareTrait;

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
   * The working directory.
   *
   * @var string
   */
  private $workingDirectory;

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
    $workingDirectory = $this->getWorkingDirectory();

    // Set working directory (if specified).
    if ($workingDirectory) {
      $process->setWorkingDirectory($workingDirectory);
    }

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
   * Return working directory.
   *
   * @return string
   *   The working directory to run the process from.
   */
  public function getWorkingDirectory() {
    return $this->workingDirectory;
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
   * @param callable|bool|null $callback
   *   A PHP callback to run whenever there is some output available on STDOUT
   *   or STDERR.
   *
   * @return Process
   *   The process that ran.
   */
  public function mustRun($successMessage = NULL, $errorMessage = NULL, $callback = NULL) {
    $process = $this->run($successMessage, $errorMessage, $callback);

    if (!$process->isSuccessful()) {
      if ($this->getOutput()->isDebug()) {
        throw new RuntimeException('Unable to run process');
      }
      else {
        throw new RuntimeException(sprintf('Unable to run process: %s', $process->getCommandLine()));
      }
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
      ->setWorkingDirectory(NULL)
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
   * @param callable|bool|null $callback
   *   A PHP callback to run whenever there is some output available on STDOUT
   *   or STDERR.
   *
   * @return Process
   *   The process that ran.
   */
  public function run($successMessage = NULL, $errorMessage = NULL, $callback = NULL) {
    $process = $this->getProcess();

    // Log process.
    $this->getLogger()->debug('<label>Starting process:</label> <code>{commandLine}</code>', array(
      'commandLine' => $this->escapeString($process->getCommandLine()),
    ));

    $process->run(function($type, $buffer) use ($callback) {
      // Display output?
      if ($callback !== FALSE) {
        // Custom callback?
        if ($callback !== NULL) {
          call_user_func($callback, $type, $buffer);
        }

        // Default callback.
        else {
          $this->getLogger()->notice('<processOutput>{output}</processOutput>', array(
            'output' => trim($buffer),
          ));
        }
      }
    });

    // Successful -> Display success message (if any).
    if ($process->isSuccessful() && $successMessage !== NULL) {
      $this->getLogger()->always('<success>{message}</success>', array(
        'message' => $successMessage,
      ));
    }

    // Not successful
    elseif (!$process->isSuccessful()) {
      // Display error output (if not already).
      if ($callback === FALSE || ($callback === NULL && !$this->getOutput()->isVerbose())) {
        $this->getLogger()->always('<processOutput>{output}</processOutput>', array(
          'output' => trim($process->getErrorOutput()),
        ));
      }
      // Display error message (if any).
      if ($errorMessage !== NULL) {
        $this->getLogger()->always('<failure>{message}</failure>', array(
          'message' => $errorMessage,
        ));
      }
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

  /**
   * Set working directory.
   *
   * @param string $workingDirectory
   *   The working directory to run the process from.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setWorkingDirectory($workingDirectory) {
    $this->workingDirectory = $workingDirectory;

    return $this;
  }

}
