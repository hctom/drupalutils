<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Theme\SetDefaultThemeTask.
 */

namespace hctom\DrupalUtils\Task\Theme;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task base class to set and enable the default theme.
 */
abstract class SetDefaultThemeTask extends EnableThemeTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:theme:set:default');
  }

  /**
   * {@inheritdoc}
   */
  protected function doExecute(InputInterface $input, OutputInterface $output) {
    // Theme is already set as default.
    if ($this->getDrupalHelper()->getDefaultTheme() === $this->getTheme()) {
      $this->getLogger()->always('<success>{name} is already set as default theme', array(
        'name' => $this->getFormatterHelper()->formatInlineCode($this->getTheme()),
      ));
    }

    // Theme was enabled.
    elseif (!parent::doExecute($input, $output)) {
      // Set default theme variable
      $this->getVariableHelper()
        ->setValue('theme_default', $this->getTheme());

      $this->getLogger()->always('<success>Set {name} as default theme', array(
        'name' => $this->getFormatterHelper()->formatInlineCode($this->getTheme()),
      ));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Set "' . $this->getTheme() . '" theme as default theme';
  }

}
