<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Filesystem\EnsureFileTask.
 */

namespace hctom\DrupalUtils\Task\Filesystem;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Task command base class to ensure a file.
 */
abstract class EnsureFileTask extends EnsureItemTask {

  /**
   * Build file content.
   *
   * @return string
   *   The file contents.
   */
  public function buildContent() {
    $templateName = $this->getTemplateName();
    $content = '';

    // Build file content from template (if any).
    if ($templateName) {
      $content = $this->getTwigHelper()->getTwig()->render($templateName, $this->getTemplateVariables());
    }

    return $content;
  }

  /**
   * Dump content into a file.
   *
   * @param string $content
   *   The data to write into the file.
   * @param $loggerMessage
   *   The logger message to display.
   * @param $loggerContext
   *   The logger context.
   */
  protected function dumpFile($content, $loggerMessage, $loggerContext) {
    $filesystem = $this->getFilesystemHelper();
    $filename = $filesystem->makePathAbsolute($this->getPath());
    $formatter = $this->getFormatterHelper();

    $filesystem->dumpFile($filename, $content);

    $this->getLogger()->notice($loggerMessage, $loggerContext);
    $this->getLogger()->debug('<label>File content:</label>');
    $this->getLogger()->debug($formatter->formatCodeBlock($content));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filesystem = $this->getFilesystemHelper();
    $filename = $filesystem->makePathAbsolute($this->getPath());
    $formatter = $this->getFormatterHelper();

    // File does not exist -> create file.
    if (!$filesystem->exists($filename)) {
      $this->dumpFile($this->buildContent($input, $output), '<label>Created file:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Existing item is not a file.
    elseif (!$filesystem->isFile($filename) && !$filesystem->isSymlinkedFile($filename)) {
      throw new RuntimeException(sprintf('"%s" is not a file', $filename));
    }

    // Skip if exists.
    elseif ($this->getSkipIfExists()) {
      $this->getLogger()->notice('<label>File already exists:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Rebuild file.
    else {
      $this->dumpFile($this->buildContent(), '<label>Rebuilt file:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Call parent to ensure permissions/group.
    $exitCode = parent::execute($input, $output);

    $this->getLogger()->always('<success>Ensured {path} file</success>', array(
      'path' => $formatter->formatPath($filename),
    ));

    return $exitCode;
  }

  /**
   * {@inheritdoc}
   */
  public function getFileMode() {
    return 0644;
  }

  /**
   * Skip if exists?
   *
   * @return bool
   *   Whether to skip the file if it already exists. Return FALSE to rebuild
   *   the existing file.
   */
  public function getSkipIfExists() {
    return $this->getFilesystemHelper()->exists($this->getPath()) && md5($this->buildContent()) === md5_file($this->getPath());
  }

  /**
   * Return template name.
   *
   * @return string|null
   *   The name of the template to generate the file. Return NULL to create an
   *   empty file (default behavior).
   */
  protected function getTemplateName() {
    return NULL;
  }

  /**
   * Return template variables.
   *
   * @return array
   *   A keyed array of additional variables that should be passed to the file
   *   creation template.
   */
  protected function getTemplateVariables() {
    return array();
  }

}
