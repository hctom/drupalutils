<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Output\OutputAwareTrait.
 */

namespace hctom\DrupalUtils\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides methods for working with the output.
 */
trait OutputAwareTrait {

  /**
   * The Output.
   *
   * @var OutputInterface
   */
  private $output;

  /**
   * Return output.
   *
   * @return OutputInterface
   *   The output.
   *
   * @throws \RuntimeException
   */
  public function getOutput() {
    if (!$this->output) {
      throw new \RuntimeException('No output has been specified');
    }

    return $this->output;
  }

  /**
   * {@inheritdoc}
   */
  public function setOutput(OutputInterface $output) {
    $this->output = $output;

    return $this;
  }

}
