<?php

// Set error reporting level.
error_reporting(E_ALL & E_STRICT);

use hctom\DrupalUtils\Console\Application;

// Handling autoloading for different use cases.
// @see https://github.com/sebastianbergmann/phpunit/blob/master/phpunit
foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
  if (file_exists($file)) {
    define('DRUPALUTILS_COMPOSER_AUTOLOAD', $file);
    break;
  }
}

unset($file);

// Provide warning, when no autoloader was found.
if (!defined('DRUPALUTILS_COMPOSER_AUTOLOAD')) {
  fwrite(STDERR,
    'You need to set up the project dependencies using the following commands:' . PHP_EOL .
    'wget http://getcomposer.org/composer.phar' . PHP_EOL .
    'php composer.phar install' . PHP_EOL
  );

  die(1);
}

require DRUPALUTILS_COMPOSER_AUTOLOAD;

// Initialize and run Application.
$app = new Application('Drupal utilities', '1.0.0');
$app->run();
