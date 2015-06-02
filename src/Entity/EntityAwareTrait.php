<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\EntityAwareTrait.
 */

namespace hctom\DrupalUtils\Entity;

use hctom\DrupalUtils\Helper\HelperSetAwareInterface;
use hctom\DrupalUtils\Input\InputAwareInterface;
use hctom\DrupalUtils\Output\OutputAwareInterface;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Provides methods for working with entities.
 */
trait EntityAwareTrait {

  /**
   * Entities.
   *
   * @var EntityInterface[]
   */
  private $entities = array();

  /**
   * Return entity.
   *
   * @param string $identifier
   *   The unique identifier of the entity to return.
   *
   * @return EntityInterface
   *   The entity.
   *
   * @throws InvalidArgumentException
   */
  public function getEntity($identifier) {
    if (!isset($this->entities[$identifier])) {
      throw new InvalidArgumentException(sprintf('Entity "%s" not found', $identifier));
    }

    return $this->entities[$identifier];
  }

  /**
   * Return all entities.
   *
   * @return EntityInterface[]
   *   An array of entities keyed by their unique identifiers.
   */
  public function getEntities() {
    return $this->entities;
  }

  /**
   * Initialize entities.
   */
  public function initializeEntities() {

  }

  /**
   * Register entity.
   *
   * @param EntityInterface $entity
   *   The entity to register.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function registerEntity(EntityInterface $entity) {
    $this->validateEntity($entity);

    // Extend interactive entity.
    if ($entity instanceof InteractiveEntityInterface) {
      // Inject input.
      if ($this instanceof InputAwareInterface) {
        $entity->setInput($this->getInput());
      }

      // Inject output.
      if ($this instanceof OutputAwareInterface) {
        $entity->setOutput($this->getOutput());
      }

      // Inject helper set.
      if ($this instanceof HelperSetAwareInterface) {
        $entity->setHelperSet($this->getHelperSet());
      }
    }

    $this->entities[$entity->getIdentifier()] = $entity;

    return $this;
  }

  /**
   * Register entities.
   *
   * @param EntityInterface[] $entities
   *   The entities to register.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function registerEntities(array $entities) {
    foreach ($entities as $entity) {
      $this->registerEntity($entity);
    }

    return $this;
  }

  /**
   * Remove entity.
   *
   * @param string $identifier
   *   The unique identifier of the entity to remove.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function removeEntity($identifier) {
    if (isset($this->entities[$identifier])) {
      unset($this->entities[$identifier]);
    }

    return $this;
  }

  /**
   * Validate entity.
   *
   * @param EntityInterface $entity
   *   The entity to validate.
   */
  public function validateEntity(EntityInterface $entity) {

  }

}
