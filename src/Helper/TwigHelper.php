<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\TwigHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Drush\SiteAliasConfig;
use hctom\DrupalUtils\Log\LoggerAwareInterface;
use hctom\DrupalUtils\Log\LoggerAwareTrait;
use hctom\DrupalUtils\Package\PackagePathAwareInterface;
use hctom\DrupalUtils\Package\PackagePathAwareTrait;
use Symfony\Component\Console\Helper\Helper;

/**
 * Provides helpers for working with Twig templates.
 */
class TwigHelper extends Helper implements LoggerAwareInterface, PackagePathAwareInterface {

  use LoggerAwareTrait;
  use PackagePathAwareTrait;

  /**
   * Return initialized Twig loader.
   *
   * @return \Twig_Loader_Filesystem
   *   The initialized Twig loader object.
   */
  protected function getLoader() {
    static $loader;

    if (!isset($loader)) {
      /* @var FormatterHelper $formatter */
      $formatter = $this->getHelperSet()->get('formatter');

      // Initialie and set up loader.
      $loader = new \Twig_Loader_Filesystem();
      $loader->addPath($this->getPackagePath() . DIRECTORY_SEPARATOR . 'tpl', 'drupalutils');

      // No additional paths in Drush site alias configuration.
      if (!($tplPaths = $this->getTemplatePaths())) {
        $this->getLogger()->debug('No template paths registered in Drush site alias configuration');
      }

      // Additional paths found in Drush site alias configuration.
      else {
        foreach ($tplPaths as $namespace => $path) {
          $namespace = ltrim($namespace, '@');

          $loader->prependPath($path, $namespace);

          $this->getLogger()
            ->debug('<label>Registered template path:</label> {namespace} ==> {baseDir}', array(
              'namespace' => $formatter->formatInlineCode('@' . $namespace),
              'baseDir' => $formatter->formatPath($path),
            ));
        }
      }
    }

    return $loader;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'twig';
  }

  /**
   * Return template paths.
   *
   * @return array
   *   A keyed array of template paths. The key is the namespace, the value is
   *   the absolute path.
   *
   * @throws \RuntimeException
   */
  protected function getTemplatePaths() {
    /* @var FilesystemHelper $filesystem */
    $filesystem = $this->getHelperSet()->get('filesystem');

    /* @var SiteAliasConfig $drushSiteAliasConfig */
    $drushSiteAliasConfig = $this->getHelperSet()->get('drush_site_alias')
      ->getConfig();

    if (($tplPaths = $drushSiteAliasConfig->getTemplatePaths())) {

      // Check template paths.
      foreach ($tplPaths as $namespace => $path) {
        if (empty($path)) {
          throw new \RuntimeException(sprintf('Empty template path specified for "%s" namespace', $namespace));
        }
        elseif (!$filesystem->isDirectory($path)) {
          throw new \RuntimeException(sprintf('Specified "%s" template path for "%s" namespace is not a directory', $path, $namespace));
        }
      }

      return $tplPaths;
    }

    return array();
  }

  /**
   * Return initialized Twig environment.
   *
   * @return \Twig_Environment
   *   The initialized Twig environment object.
   */
  public function getTwig() {
    static $twig;

    if (!isset($engine)) {
      /* @var DrupalHelper $drupal */
      $drupal = $this->getHelperSet()->get('drupal');

      $twig = new \Twig_Environment($this->getLoader(), array(
        'autoescape' => FALSE,
      ));

      // Add filter: var_export.
      $twig->addFilter(new \Twig_SimpleFilter('var_export', function($var) {
        $export = var_export($var, TRUE);
        $export = preg_replace('/array\s+\(/', 'array(', $export);
        $export = preg_replace('/array\(\n\)/m', 'array()', $export);

        return $export;
      }));

      // Add global 'drupalutils' variable.
      $twig->addGlobal('drupalutils', (object) array(
        'environment' => $drupal->getEnvironment(),
      ));
    }

    return $twig;
  }

}
