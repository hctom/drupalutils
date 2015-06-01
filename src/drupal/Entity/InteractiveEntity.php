<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\InteractiveEntity.
 */

namespace hctom\DrupalUtils\Entity;

use hctom\DrupalUtils\Helper\FormatterHelper;
use hctom\DrupalUtils\Helper\HelperSetAwareTrait;
use hctom\DrupalUtils\Input\InputAwareTrait;
use hctom\DrupalUtils\Output\OutputAwareTrait;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

/**
 * Base class for interactive entity objects.
 */
abstract class InteractiveEntity extends Entity implements InteractiveEntityInterface {

  use HelperSetAwareTrait;
  use InputAwareTrait;
  use OutputAwareTrait;

  /**
   * Asks a question.
   *
   * @param Question $question
   *   The question to ask.
   *
   * @return string
   *   The answer.
   */
  protected function ask(Question $question) {
    $input = $this->getInput();

    // Ensure interactive mode (while saving old state).
    $oldInteractiveState = $input->isInteractive();
    $input->setInteractive(TRUE);

    $answer =  $this->getQuestionHelper()->ask($input, $this->getOutput(), $question);

    // Reset interactive mode to old value.
    $input->setInteractive($oldInteractiveState);

    return $answer;
  }

  /**
   * Get formatter helper.
   *
   * @return FormatterHelper
   *   The formatter helper object.
   */
  protected function getFormatterHelper() {
    return $this->getHelperSet()->get('formatter');
  }

  /**
   * Get question helper.
   *
   * @return QuestionHelper
   *   The question helper object.
   */
  public function getQuestionHelper() {
    return $this->getHelperSet()->get('question');
  }

}
