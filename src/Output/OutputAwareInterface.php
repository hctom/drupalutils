<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Output\OutputAwareInterface.
 */

namespace hctom\DrupalUtils\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Should be implemented by classes that depend on the output.
 */
interface OutputAwareInterface {

  /**
   * Return output.
   *
   * @return OutputInterface
   *   The output.
   */
  public function getOutput();

  /**
   * Set output.
   *
   * @param OutputInterface $output
   *   The output.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function setOutput(OutputInterface $output);

}
