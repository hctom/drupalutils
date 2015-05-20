<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Task\Environment\EnsureEnvSettingsTask.
 */

namespace hctom\DrupalUtils\Task\Environment;

use hctom\DrupalUtils\Task\Filesystem\EnsureSettingsFileTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a task command to create an environment specific settings.php file
 * (if not exists).
 */
class EnsureEnvSettingsTask extends EnsureSettingsFileTask {

  /**
   * {@inheritdoc}
   */
  public function buildContent(InputInterface $input, OutputInterface $output) {
    $content = array();

    // TODO Use template engine
    // Build file content.
    $content[] = <<<EOT
<?php

/**
 * @file
 * Settings file for the '{$this->getDrupalHelper()->getEnvironment()}' environment.
 */

// Drupal default settings.
require '{$this->getFilesystemHelper()->makePathRelative($this->getFilesystemHelper()->makePathAbsolute('sites/default/default.settings.php'))}';
EOT;

    // Add includes to file content.
    foreach ($this->getIncludes() as $include) {
      $path = $this->getFilesystemHelper()->makePathRelative($this->getFilesystemHelper()->makePathAbsolute($include['path']));

      $content[] = <<<EOT
// {$include['comment']}.
if (file_exists('{$path}')) {
  require '{$path}';
}
EOT;
    }

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('task:environment:settings:ensure');
  }

  // TODO Docs
  public function getIncludes() {
    return array(
      'database' => array(
        'path' => $this->getSiteSettingsDirectory() . DIRECTORY_SEPARATOR . 'db.' . $this->getDrupalHelper()->getEnvironment() . '.inc',
        'comment' => 'Database settings',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->getDrupalHelper()->getSiteDirectoryPath() . DIRECTORY_SEPARATOR . 'settings.' . $this->getDrupalHelper()->getEnvironment() . '.php';
  }

  /**
   * Return task title.
   *
   * @return string
   *   The human-readable title of the task.
   */
  public function getTitle() {
    return 'Ensure environment specific settings.php file';
  }

}
