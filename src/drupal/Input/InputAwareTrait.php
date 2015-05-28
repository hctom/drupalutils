<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Input\InputAwareTrait.
 */

namespace hctom\DrupalUtils\Input;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Provides methods for working with the input.
 */
trait InputAwareTrait {

  /**
   * The Input.
   *
   * @var InputInterface
   */
  private $input;

  /**
   * Return input.
   *
   * @return InputInterface
   *   The input.
   *
   * @throws \RuntimeException
   */
  public function getInput() {
    if (!$this->input) {
      throw new \RuntimeException('No input has been specified');
    }

    return $this->input;
  }

  /**
   * {@inheritdoc}
   */
  public function setInput(InputInterface $input) {
    $this->input = $input;

    return $this;
  }

}
