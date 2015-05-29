<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Theme\DisableThemeTask.
 */

namespace hctom\DrupalUtils\Task\Theme;

use hctom\DrupalUtils\Task\Project\DisableProjectTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Task base class for disabling a theme.
 */
abstract class DisableThemeTask extends DisableProjectTask {

  /**
   * {@inheritdoc}
   */
  protected function doExecute(InputInterface $input, OutputInterface $output) {
    $themeName = $this->getTheme();

    // Theme to disable is default theme.
    if ($this->getDrupalHelper()->getDefaultTheme() === $themeName) {
      throw new RuntimeException(sprintf('"%s" is the default theme and cannot be disabled', $themeName));
    }

    return parent::doExecute($input, $output);
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectNames() {
    return array($this->getTheme());
  }

  /**
   * Return theme name.
   *
   * @return string
   *   The name of the theme to disable.
   */
  abstract public function getTheme();

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Disable "' . $this->getTheme() . '" theme';
  }

}
