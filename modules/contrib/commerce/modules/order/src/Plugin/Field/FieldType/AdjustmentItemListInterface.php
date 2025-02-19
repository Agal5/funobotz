<?php

namespace Drupal\commerce_order\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\commerce_order\Adjustment;

/**
 * Represents a list of adjustment item field values.
 */
interface AdjustmentItemListInterface extends FieldItemListInterface {

  /**
   * Gets the adjustment value objects from the field list.
   *
   * @return \Drupal\commerce_order\Adjustment[]
   *   An array of adjustment value objects.
   */
  public function getAdjustments();

  /**
   * Removes the matching adjustment value.
   *
   * @param \Drupal\commerce_order\Adjustment $adjustment
   *   The adjustment.
   */
  public function removeAdjustment(Adjustment $adjustment);

}
