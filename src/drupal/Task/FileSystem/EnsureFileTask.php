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
   * @param InputInterface $input
   *   The input.
   * @param OutputInterface $output
   *   The output.
   *
   * @return string
   *   The file contents.
   */
  public function buildContent(InputInterface $input, OutputInterface $output) {
    $templateName = $this->getTemplateName();
    $content = '';

    // Build file content from template (if any).
    if ($templateName) {
      $content = $this->getTwigHelper()->getTwig()->render($templateName, $this->getTemplateVariables($input, $output));
    }

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filename = $this->getFilesystemHelper()->makePathAbsolute($this->getPath());
    $filesystem = $this->getFilesystemHelper();
    $formatter = $this->getFormatterHelper();
    $fileMode = $this->getFileMode();

    // File does not exist -> create file.
    if (!$filesystem->exists($filename)) {
      $content = $this->buildContent($input, $output);
      $filesystem->dumpFile($filename, $content, $fileMode);

      $this->getLogger()->notice('<label>Created file:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));

      $this->getLogger()->debug('<label>File content:</label>');
      $this->getLogger()->debug($formatter->formatCodeBlock($content));
    }

    // Existing item is not a file.
    elseif (!$filesystem->isFile($filename)) {
      throw new RuntimeException(sprint('"%s" is not a file', $filename));
    }

    // Skip if exists.
    elseif ($this->getSkipIfExists()) {
      $this->getLogger()->notice('<label>File already exists:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));
    }

    // Rebuild file.
    else {
      $content = $this->buildContent($input, $output);
      $filesystem->dumpFile($filename, $content, $fileMode);

      $this->getLogger()->notice('<label>Rebuilt file:</label> {path}', array(
        'path' => $formatter->formatPath($filename),
      ));

      $this->getLogger()->debug('<label>File content:</label>');
      $this->getLogger()->debug($formatter->formatCodeBlock($content));
    }

    // Call parent to ensure permissions/group.
    $exitCode = parent::execute($input, $output);

    $this->getLogger()->always('<success>Ensured {path} file</success>', array(
      'path' => $this->getFormatterHelper()->formatPath($filename),
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
    return TRUE;
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
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return array
   *   A keyed array of additional variables that should be passed to the file
   *   creation template.
   */
  protected function getTemplateVariables(InputInterface $input, OutputInterface $output) {
    return array();
  }

}
