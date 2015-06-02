<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Theme\SetAdminThemeTask.
 */

namespace hctom\DrupalUtils\Task\Theme;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Task base class to set and optionally enable the administrative theme.
 */
abstract class SetAdminThemeTask extends EnableThemeTask {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:theme:set:admin');
  }

  /**
   * {@inheritdoc}
   */
  protected function doExecute(InputInterface $input, OutputInterface $output) {
    $formatter = $this->getFormatterHelper();
    $nodeAdminTheme = $this->getNodeAdminTheme();

    // Theme is already set as admin theme.
    if ($this->getDrupalHelper()->getAdminTheme() === $this->getTheme()) {
      $this->getLogger()->always('<success>{name} is set as administrative theme', array(
        'name' => $formatter->formatInlineCode($this->getTheme()),
      ));
    }

    // Unable to enable theme (if needed)
    elseif ($this->getEnableTheme() && ($exitCode = parent::doExecute($input, $output))) {
      return $exitCode;
    }

    // Unable to set administrative theme variable.
    elseif (($exitCode = $this->getVariableHelper()->setValue('admin_theme', $this->getTheme()))) {
      return $exitCode;
    }

    else {
      $this->getLogger()->always('<success>Set {name} as administrative theme', array(
        'name' => $formatter->formatInlineCode($this->getTheme()),
      ));
    }

    // Use the administration theme when editing or creating content.
    if ($nodeAdminTheme !== NULL) {
      $nodeAdminTheme = (int) $nodeAdminTheme;
      // Content administration theme settings is configured.
      if ((int) $this->getVariableHelper()->getValue('node_admin_theme') === $nodeAdminTheme) {
        if ($nodeAdminTheme) {
          $this->getLogger()->always('<success>Administrative theme is used when creating/editing content</success>');
        }
        else {
          $this->getLogger()->always('<success>Administrative theme is not used when creating/editing content</success>');
        }
      }

      // Unable to toggle content administration theme setting.
      elseif (($exitCode = $this->getVariableHelper()->setValue('node_admin_theme', $this->getNodeAdminTheme()))) {
        return $exitCode;
      }

      else {
        if ($nodeAdminTheme) {
          $this->getLogger()->always('<success>Set administrative theme is used when creating/editing content</success>');
        }
        else {
          $this->getLogger()->always('<success>Set administrative theme is not used when creating/editing content</success>');
        }
      }
    }
  }

  /**
   * Enable administrative theme?
   *
   * @return bool
   *   Whether to enable the administrative theme.
   */
  public function getEnableTheme() {
    return FALSE;
  }

  /**
   * Return administrative theme is sued when editing or creating content?
   *
   * @return bool|null
   *   Whether to use the administrative theme when creating/editing content.
   *   Return NULL to perform no change.
   */
  public function getNodeAdminTheme() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return 'Set "' . $this->getTheme() . '" theme as administrative theme';
  }

}
