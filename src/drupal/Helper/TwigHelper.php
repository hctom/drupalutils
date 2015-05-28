<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\TwigHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Package\PackagePathAwareInterface;
use hctom\DrupalUtils\Package\PackagePathAwareTrait;
use Symfony\Component\Console\Helper\Helper;

/**
 * Provides helpers for working with Twig templates.
 */
class TwigHelper extends Helper implements PackagePathAwareInterface {

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
      $loader = new \Twig_Loader_Filesystem();
      $loader->addPath($this->getPackagePath() . DIRECTORY_SEPARATOR . 'tpl', 'drupalutils');

      // TODO Allow additional path sot be specified in Drush site alias config.
      // $loader->prependPath()
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
