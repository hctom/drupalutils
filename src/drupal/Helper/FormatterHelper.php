<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\FormatterHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Console\Terminal\TerminalDimensionsAwareInterface;
use hctom\DrupalUtils\Console\Terminal\TerminalDimensionsAwareTrait;
use hctom\DrupalUtils\Output\OutputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use hctom\DrupalUtils\Task\TaskInterface;
use Symfony\Component\Console\Helper\FormatterHelper as SymfonyFormatterHelper;

/**
 * Provides helpers to format messages.
 */
class FormatterHelper extends SymfonyFormatterHelper implements OutputAwareInterface, TerminalDimensionsAwareInterface {

  use OutputAwareTrait;
  use TerminalDimensionsAwareTrait;

  /**
   * Format source code.
   *
   * @param string $code
   *   The source code.
   * @return string
   *   The formatted source code.
   */
  function formatCode($code) {
    return '<code>' . $code . '</code>';
  }

  /**
   * Format a message as a block of text that fills the full width of the terminal.
   *
   * @param string|array $messages
   *   The message(s) to write in the block.
   * @param string $style
   *   The style to apply to the whole block.
   * @param bool $large
   *   Whether to return a large block
   *
   * @return string
   *   The formatted message.
   */
  public function formatFullWidthBlock($messages, $style, $large = FALSE) {
    if (!is_array($messages)) {
      $messages = array($messages);
    }

    foreach ($messages as &$message) {
      $message = str_pad($message, $this->getTerminalWidth() - ($large ? 4 : 2), ' ', STR_PAD_RIGHT);
    }

    return $this->formatBlock($messages, $style, $large);
  }

  /**
   * Format label.
   *
   * @param string $label
   *   The label text
   * @return string
   *   The formatted label.
   */
  function formatLabel($label) {
    return '<label>' . $label . '</label>';
  }

  /**
   * Format path.
   *
   * @param string $path
   *   The path.
   * @return string
   *   The formatted path.
   */
  function formatPath($path) {
    /* @var DrupalHelper $drupal */
    $drupal = $this->getHelperSet()->get('drupal');

    /* @var FilesystemHelper $filesystem */
    $filesystem = $this->getHelperSet()->get('filesystem');

    // Path is abosolute?
    if ($filesystem->isAbsolutePath($path)) {
      // Make path relative (if not debug).
      if (!$this->getOutput()->isDebug()) {
        $path = $filesystem->makePathRelative($path);

        // Remove './' prefix (if any).
        if (preg_match('!^' . preg_quote('./', '!') . '!', $path)) {
          $path = substr($path, 2);
        }
      }
    }

    return '<path>' . $path . '</path>';
  }

  /**
   * Format task information.
   *
   * @param TaskInterface $task
   *   The task object.
   * @param $count
   *   The current task count.
   * @param $total
   *   The total number of tasks.
   *
   * @return string
   *   The formatted task information.
   */
  function formatTaskInfo(TaskInterface $task, $count, $total) {
    // Build counter.
    $countFormat = '%0' . $this->strlen($total) . 'd';
    $counter = '  ' . sprintf($countFormat, $count) . '/' . sprintf($countFormat, $total) . '  ';

    // Prepare information to display.
    $info = array(
      '',
      '<label>' . $task->getTitle() . '</label>',
    );

    if ($this->getOutput()->isDebug()) {
      $info = array_merge($info, array(
        '',
        $this->formatLabel('<label>Name:</label>') . '   ' . $this->formatCode($task->getName()),
        $this->formatLabel('<label>Class:</label>') . '  ' . $this->formatCode(get_class($task)),
      ));
    }

    $info[] = '';

    // Build message lines.
    $lines = array();

    for ($i = 0; $i < max(3, count($info)); $i++) {
      $line = '';
      if ($i === 1) {
        $line .= '<counter>' . $counter . '</counter>';
      }
      else {
        $line .= '<counter>' . str_pad('', $this->strlen($counter), ' ') . '</counter>';
      }

      if (isset($info[$i])) {
        $line .= '  ' . $info[$i];
      }

      $lines[] = $line;
    }

    return "\n" . implode("\n", $lines) . "\n";
  }

}
