<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Input\InputAwareInterface.
 */

namespace hctom\DrupalUtils\Input;

use Symfony\Component\Console\Input\InputAwareInterface as SymfonyConsoleInputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Should be implemented by classes that depend on the input.
 */
interface InputAwareInterface extends SymfonyConsoleInputAwareInterface {

  /**
   * Return input.
   *
   * @return InputInterface
   *   The output.
   */
  public function getInput();

}
