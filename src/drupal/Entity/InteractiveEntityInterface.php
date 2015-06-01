<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\InteractiveEntityInterface.
 */

namespace hctom\DrupalUtils\Entity;

use hctom\DrupalUtils\Helper\HelperSetAwareInterface;
use hctom\DrupalUtils\Input\InputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareInterface;

/**
 * Should be implemented by interactive entity classes.
 */
interface InteractiveEntityInterface extends EntityInterface, HelperSetAwareInterface, InputAwareInterface, OutputAwareInterface {

}
