<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Helper\DrushProcessHelper.
 */

namespace hctom\DrupalUtils\Helper;

use hctom\DrupalUtils\Drush\SiteAliasAwareInterface;
use hctom\DrupalUtils\Drush\SiteAliasAwareTrait;
use hctom\DrupalUtils\Package\PackagePathAwareInterface;
use hctom\DrupalUtils\Package\PackagePathAwareTrait;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Provides helpers to run external Drush processes.
 */
class DrushProcessHelper extends ProcessHelper implements SiteAliasAwareInterface, PackagePathAwareInterface {

  use PackagePathAwareTrait;
  use SiteAliasAwareTrait;

  /**
   * Assumed answer: No.
   */
  const ASSUMED_ANSWER_NO = 'no';

  /**
   * Assumed answer: Yes.
   */
  const ASSUMED_ANSWER_YES = 'yes';

  /**
   * Assumed answer to all prompt.
   *
   * @var string
   */
  private $assumedAnswerToAllPrompts;

  /**
   * Answer 'no' to all prompts.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function answerNoToAllPrompts() {
    return $this->setAssumedAnswerToAllPrompts(static::ASSUMED_ANSWER_NO);
  }

  /**
   * Answer 'yes' to all prompts.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function answerYesToAllPrompts() {
    return $this->setAssumedAnswerToAllPrompts(static::ASSUMED_ANSWER_YES);
  }

  /**
   * {@inheritdoc}
   */
  protected function buildOptions() {
    $options = parent::buildOptions();

    // Assume 'yes' or 'no' as answer to all prompts.
    $assumedAnswer = $this->getAssumedAnswerToAllPrompts();
    $options[$assumedAnswer] = '--' . $assumedAnswer;

    // Add include path(s).
    $options['include'] = '--include=' . implode(',', $this->getIncludePaths());

    return $options;
  }

  /**
   * Return assumed answer to all prompts.
   *
   * @return string
   *   The assumed answer to all prompts. Possible values:
   *     - static::ASSUMED_ANSWER_NO: No.
   *     - static::ASSUMED_ANSWER_YES: Yes.
   */
  public function getAssumedAnswerToAllPrompts() {
    return empty($this->assumedAnswerToAllPrompts) ? static::ASSUMED_ANSWER_YES : $this->assumedAnswerToAllPrompts;
  }

  /**
   * Return Drush command include paths.
   *
   * @return array
   *   An array of include paths to consider when looking for Drush commands
   */
  protected function getIncludePaths() {
    $includePaths = array(
      $this->getPackagePath() . DIRECTORY_SEPARATOR . 'drush' . DIRECTORY_SEPARATOR . 'commands',
    );

    return $includePaths;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'drush_process';
  }

  /**
   * {@inheritdoc}
   */
  protected function getProcessBuilder() {
    $processBuilder = parent::getProcessBuilder();

    // Override process builder prefix.
    $processBuilder->setPrefix(array(
      'drush',
      $this->getSiteAlias(),
      $this->getCommandName(),
    ));

    return $processBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    return parent::reset()
      ->setAssumedAnswerToAllPrompts(static::ASSUMED_ANSWER_YES);
  }

  /**
   * Set assumed answer to all prompts.
   *
   * @param string $answer
   *   The assumed answer to all prompts. Possible values:
   *     - static::ASSUMED_ANSWER_NO: No.
   *     - static::ASSUMED_ANSWER_YES: Yes.
   *
   * @return static
   *   A self-reference for method chaining.
   *
   * @throws InvalidArgumentException
   */
  public function setAssumedAnswerToAllPrompts($answer) {
    $allowed = array(
      static::ASSUMED_ANSWER_NO,
      static::ASSUMED_ANSWER_YES,
    );

    // Assumed answer is not allowed.
    if (!in_array($answer, $allowed)) {
      throw new InvalidArgumentException(sprintf('Invalid assumed answer "%"', $answer));
    }

    $this->assumedAnswerToAllPrompts = $answer;

    return $this;
  }

}
