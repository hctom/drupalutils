<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Drush\DrushCommand.
 */

namespace hctom\DrupalUtils\Drush;

use hctom\DrupalUtils\Command\Command;
use hctom\DrupalUtils\Process\DrushProcessBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities command class: Drush command.
 */
class DrushCommand extends Command {

  use DrushSiteAliasAwareTrait;

  /**
   * Temporary output pointer.
   *
   * @var OutputInterface
   */
  private $___output;

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $processBuilder = new DrushProcessBuilder();

    // Set up process build.
    $process = $processBuilder
      ->setDrushSiteAlias($this->getDrushSiteAlias())
      ->setArguments($input->getArguments())
      ->setOptions($input->getOptions())
      ->getProcess();

    // Debug: Output Drush command.
    if ($output->isDebug()) {
      // TODO Implement a logger for this?
      $output->writeln('<comment>Drush:</comment> ' . $process->getCommandLine());
    }

    // Run process and display output (utilizing temporary output pointer to
    // avoid namespacing conflicts).
    $this->___output = $output;
    $process->run(function($type, $buffer) {
      if ($type !== Process::ERR) {
        $this->___output->write($buffer);
      }
    });
    unset($this->___output);

    // Error occured?
    if (!$process->isSuccessful()) {
      $output->write($process->getErrorOutput());
    }

    return $process->getExitCode();
  }

}
