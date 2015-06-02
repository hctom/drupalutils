<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\InteractiveEntityAwareInterface.
 */

namespace hctom\DrupalUtils\Entity;

use hctom\DrupalUtils\Helper\HelperSetAwareInterface;
use hctom\DrupalUtils\Input\InputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareInterface;

/**
 * Should be implemented by classes that depend on interactive entities.
 */
interface InteractiveEntityAwareInterface extends EntityAwareInterface, HelperSetAwareInterface, InputAwareInterface, OutputAwareInterface {

}
