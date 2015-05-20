<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Console\Application.
 */

namespace hctom\DrupalUtils\Console;

use hctom\DrupalUtils\Command\Command;
use hctom\DrupalUtils\Command\Site\InstallSiteCommand;
use hctom\DrupalUtils\Drush\SiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\SiteAliasConfig;
use hctom\DrupalUtils\Console\Terminal\TerminalDimensionsAwareInterface;
use hctom\DrupalUtils\Helper\DrupalHelper;
use hctom\DrupalUtils\Helper\DrushHelper;
use hctom\DrupalUtils\Helper\DrushProcessHelper;
use hctom\DrupalUtils\Helper\DrushSiteAliasHelper;
use hctom\DrupalUtils\Helper\FilesystemHelper;
use hctom\DrupalUtils\Helper\FormatterHelper;
use hctom\DrupalUtils\Log\Logger;
use hctom\DrupalUtils\Log\LoggerInterface;
use hctom\DrupalUtils\Output\OutputAwareInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\ClassLoader\Psr4ClassLoader;
use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal utilities application class.
 */
class Application extends SymfonyConsoleApplication implements LoggerAwareInterface {

  use LoggerAwareTrait;

  /**
   * Logo.
   *
   * @var string
   */
  private static $logo =
<<<EOT
       __                            __        __   _  __
  ____/ /_____ __  __ ____   ____ _ / /__  __ / /_ (_)/ /_____
 / __  // ___// / / // __ \ / __ `// // / / // __// // // ___/
/ /_/ // /   / /_/ // /_/ // /_/ // // /_/ // /_ / // /(__  )
\__,_//_/    \__,_// .___/ \__,_//_/ \__,_/ \__//_//_//____/
                  /_/

EOT;

  /**
   * Add commands from Drush site alias configuration.
   */
  protected function addCommandsFromDrushSiteAliasConfig() {
    // Fetch Drush site alias details.
    /* @var SiteAliasConfig $drushSiteAliasConfig */
    $drushSiteAliasConfig = $this->getHelperSet()->get('drush_site_alias')
      ->getConfig();

    if (!($commandConfig = $drushSiteAliasConfig->getCommands())) {
      $this->getLogger()->debug('No commands registered in Drush site alias configuration');

      return;
    }

    // Process command list.
    foreach ($commandConfig as $commandName => $class) {
      /* @var FormatterHelper $formatter */
      $formatter = $this->getHelperSet()->get('formatter');

      // Command class exists?
      if (!class_exists($class)) {
        throw new \RuntimeException('Registered command class not found: ' . $class);
      }

      /* @var Command $command */
      $command = new $class();

      // Command is not an instance of Symfony console command class?
      if (!$command instanceof SymfonyConsoleCommand) {
        throw new \RuntimeException('Registered invalid command class ' . $class . ' for ' . $commandName);
      }

      // Log overridden command.
      if ($this->has($command->getName())) {
        $this->getLogger()
          ->debug('<label>Registered overriding command:</label> {command} ==> {class}', array(
            'command' => $formatter->formatCode($command->getName()),
            'class' => $formatter->formatCode(ltrim($class, '\\')),
          ));
      }

      // Log new command.
      else {
        $this->getLogger()
          ->debug('<label>Registered new command:</label> {command} ==> {class}', array(
            'command' => $formatter->formatCode($command->getName()),
            'class' => $formatter->formatCode(ltrim($class, '\\')),
          ));
      }

      // Add custom command.
      $this->add($command);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function configureIO(InputInterface $input, OutputInterface $output) {
    parent::configureIO($input, $output);

    // Configure output formatter styles.
    $this->configureOutputFormatterStyles($output);

    // Configure logger (if not already).
    $this->configureLogger($output);

    // Extend helpers.
    $this->extendHelpers($input, $output);
  }

  /**
   * Configure logger.
   *
   * @param OutputInterface $output
   *   The Output.
   */
  protected function configureLogger(OutputInterface $output) {
    // Create and set up logger (if not already).
    if (!$this->getLogger()) {
      // Create and set up line formatter (allowing inline line breaks).
      $format = "%message%\n";
      $formatter = new LineFormatter($format, NULL, TRUE);

      // Create and set up console handler.
      $handler = new ConsoleHandler($output, TRUE, array(
        OutputInterface::VERBOSITY_NORMAL => Logger::ALWAYS,
        OutputInterface::VERBOSITY_VERBOSE => Logger::NOTICE,
        OutputInterface::VERBOSITY_VERY_VERBOSE => Logger::INFO,
        OutputInterface::VERBOSITY_DEBUG => Logger::DEBUG,
      ));
      $handler->setFormatter($formatter);

      // Create and set up logger.
      $logger = new Logger($this->getName());
      $logger->pushProcessor(new PsrLogMessageProcessor());
      $logger->pushHandler($handler);

      // Assign logger.
      $this->setLogger($logger);
    }
  }

  /**
   * Configure output formatter styles.
   *
   * @param OutputInterface $output
   *   An Output instance.
   */
  protected function configureOutputFormatterStyles(OutputInterface $output) {
    // Code.
    $output->getFormatter()->setStyle('code', new OutputFormatterStyle('cyan'));

    // Code.
    $output->getFormatter()->setStyle('counter', new OutputFormatterStyle('white', 'blue', array('bold')));

    // Failure.
    $output->getFormatter()->setStyle('failure', new OutputFormatterStyle('red', NULL));

    // Label.
    $output->getFormatter()->setStyle('label', new OutputFormatterStyle('white', NULL, array('bold')));

    // Path.
    $output->getFormatter()->setStyle('path', new OutputFormatterStyle('yellow'));

    // Process output.
    $output->getFormatter()->setStyle('processOutput', new OutputFormatterStyle('white'));

    // Success.
    $output->getFormatter()->setStyle('success', new OutputFormatterStyle('green'));

    // Table of contents.
    $output->getFormatter()->setStyle('toc', new OutputFormatterStyle('white', 'blue'));

    // Warning.
    $output->getFormatter()->setStyle('warning', new OutputFormatterStyle('magenta'));
  }

  /**
   * {@inheritdoc}
   */
  public function doRun(InputInterface $input, OutputInterface $output) {
    // Initialize class loader.
    $this->initializeClassLoader();

    // Merge additional/overriding commands from Drush site alias configuration.
    $this->addCommandsFromDrushSiteAliasConfig();

    return parent::doRun($input, $output);
  }

  /**
   * Extend helper objects.
   *
   * @param InputInterface $input
   *   THe input.
   * @param OutputInterface $output
   *   The output.
   */
  protected function extendHelpers(InputInterface $input, OutputInterface $output) {
    // Determine Drush site alias.
    $drushSiteAlias = $this->getDrushSiteAliasFromInput($input);

    // Extend helpers.
    foreach ($this->getHelperSet() as $helper) {
      // Inject Drush site alias into helper.
      if ($helper instanceof SiteAliasAwareInterface) {
        $helper->setSiteAlias($drushSiteAlias);
      }

      // Inject output into helper.
      if ($helper instanceof OutputAwareInterface) {
        $helper->setOutput($output);
      }

      // Inject logger into helper.
      if ($helper instanceof LoggerAwareInterface) {
        $helper->setLogger($this->getLogger());
      }

      // Inject terminal dimensions.
      if ($helper instanceof TerminalDimensionsAwareInterface) {
        $helper->setTerminalDimensions($this->getTerminalDimensions());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultCommands() {
    $defaultCommands = parent::getDefaultCommands();

    // Install site.
    $defaultCommands[] = new InstallSiteCommand();

    return $defaultCommands;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultHelperSet() {
    $helperSet = parent::getDefaultHelperSet();

    // Drupal helper.
    $helperSet->set(new DrupalHelper());

    // Drush helper.
    $helperSet->set(new DrushHelper());

    // Drush process helper.
    $helperSet->set(new DrushProcessHelper());

    // Drush site alias helper.
    $helperSet->set(new DrushSiteAliasHelper());

    // File system helper.
    $helperSet->set(new FilesystemHelper());

    // Formatter helper.
    $helperSet->set(new FormatterHelper());

    return $helperSet;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultInputDefinition() {
    $inputDefinition = parent::getDefaultInputDefinition();

    // Add additional options.
    $inputDefinition->addOptions(array(
      // Drush site alias.
      new InputOption('--site', NULL, InputOption::VALUE_REQUIRED, 'The Drush site alias to use.', '@none'),
    ));

    return $inputDefinition;
  }


  /**
   * Return Drush site alias from input.
   *
   * @param InputInterface $input
   *   The input.
   *
   * @return string
   *   The Drush site alias (defaults to '@none').
   */
  protected function getDrushSiteAliasFromInput(InputInterface $input) {
    $siteAlias = $siteAliasDefault = '@none';

    // Inspect input for Drush site alias.
    if ($input->hasOption('site')) {
      $siteAlias = $input->getOption('site');
    }
    elseif ($input->hasParameterOption('--site')) {
      $siteAlias = $input->getParameterOption('--site', $siteAliasDefault);
    }

    return trim($siteAlias) ? trim($siteAlias) : $siteAliasDefault;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return self::$logo . parent::getHelp();
  }

  /**
   * Return logger.
   *
   * @return LoggerInterface
   *   The logger.
   */
  public function getLogger() {
    return $this->logger;
  }

  /**
   * Initialize class loader.
   */
  protected function initializeClassLoader() {
    /* @var FormatterHelper $formatter */
    $formatter = $this->getHelperSet()->get('formatter');

    /* @var SiteAliasConfig $drushSiteAliasConfig */
    $drushSiteAliasConfig = $this->getHelperSet()->get('drush_site_alias')
      ->getConfig();

    // No class namespaces in Drush site alias configuration?
    if (!($classLoaderConfig = $drushSiteAliasConfig->getClassLoaderNamespaces())) {
      $this->getLogger()->debug('No class loader prefixes registered in Drush site alias configuration');

      return;
    }

    // Initialize class loaders.
    $classLoaders = array(
      'psr-4' => new Psr4ClassLoader(),
    );

    // Register namespaces.
    foreach ($classLoaders as $autoLoadStandard => $classLoader) {
      switch ($autoLoadStandard) {
        case 'psr-4':
          if (($namespaces = $drushSiteAliasConfig->getPsr4ClassLoaderNamespaces())) {
            foreach ($namespaces as $namespace => $path) {
              $classLoader->addPrefix($namespace, $path);

              $this->getLogger()
                ->debug('<label>Registered PSR-4 class loader prefix:</label> {prefix} ==> {baseDir}', array(
                  'prefix' => $formatter->formatCode(rtrim($namespace, '\\')),
                  'baseDir' => $formatter->formatPath($path),
                ));
            }

            $classLoader->register();
          }
          break;
      }
    }
  }

}
