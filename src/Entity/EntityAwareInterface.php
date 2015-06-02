<?php

/**
 * @file
 * Contains hctom\DrupalUtils\Entity\EntityAwareInterface.
 */

namespace hctom\DrupalUtils\Entity;

use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Should be implemented by classes that depend on entities.
 */
interface EntityAwareInterface {

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
  public function getEntity($identifier);

  /**
   * Return all entities.
   *
   * @return EntityInterface[]
   *   An array of entities keyed by their unique identifiers.
   */
  public function getEntities();

  /**
   * Initialize entities.
   */
  public function initializeEntities();

  /**
   * Register entity.
   *
   * @param EntityInterface $entity
   *   The entity to register.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function registerEntity(EntityInterface $entity);

  /**
   * Register entities.
   *
   * @param EntityInterface[] $entities
   *   The entities to register.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function registerEntities(array $entities);

  /**
   * Remove entity.
   *
   * @param string $identifier
   *   The unique identifier of the entity to remove.
   *
   * @return static
   *   A self-reference for method chaining.
   */
  public function removeEntity($identifier);

  /**
   * Validate entity.
   *
   * @param EntityInterface $entity
   *   The entity to validate.
   */
  public function validateEntity(EntityInterface $entity);

}
