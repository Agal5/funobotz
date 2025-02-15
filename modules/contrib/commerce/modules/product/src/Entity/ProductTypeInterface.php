<?php

namespace Drupal\commerce_product\Entity;

use Drupal\Core\Entity\EntityDescriptionInterface;
use Drupal\commerce\Entity\CommerceBundleEntityInterface;

/**
 * Defines the interface for product types.
 */
interface ProductTypeInterface extends CommerceBundleEntityInterface, EntityDescriptionInterface {

  /**
   * Gets the product type's matching variation type IDs.
   *
   * @return array
   *   The variation type ID.
   */
  public function getVariationTypeIds() : array;

  /**
   * Sets the product type's matching variation type IDs.
   *
   * @param array $variation_type_ids
   *   The variation type IDs.
   *
   * @return $this
   */
  public function setVariationTypeIds(array $variation_type_ids) : self;

  /**
   * Gets whether products of this type can have multiple variations.
   *
   * @return bool
   *   TRUE if products of this type can have multiple variations,
   *   FALSE otherwise.
   */
  public function allowsMultipleVariations() : bool;

  /**
   * Sets whether products of this type can have multiple variations.
   *
   * @param bool $multiple_variations
   *   Whether products of this type can have multiple variations.
   *
   * @return $this
   */
  public function setMultipleVariations(bool $multiple_variations) : self;

  /**
   * Gets whether variation fields should be injected into the rendered product.
   *
   * @return bool
   *   TRUE if the variation fields should be injected into the rendered
   *   product, FALSE otherwise.
   */
  public function shouldInjectVariationFields() : bool;

  /**
   * Sets whether variation fields should be injected into the rendered product.
   *
   * @param bool $inject
   *   Whether variation fields should be injected into the rendered product.
   *
   * @return $this
   */
  public function setInjectVariationFields(bool $inject) : self;

}
