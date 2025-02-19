<?php

namespace Drupal\commerce_order\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\commerce_order\Adjustment;

/**
 * Represents a list of adjustment item field values.
 */
class AdjustmentItemList extends FieldItemList implements AdjustmentItemListInterface {

  /**
   * {@inheritdoc}
   */
  public function getAdjustments() {
    $adjustments = [];
    /** @var \Drupal\commerce_order\Plugin\Field\FieldType\AdjustmentItem $field_item */
    foreach ($this->list as $key => $field_item) {
      if (!$field_item->isEmpty()) {
        $adjustments[$key] = $field_item->value;
      }
    }

    return $adjustments;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAdjustment(Adjustment $adjustment) {
    /** @var \Drupal\commerce_order\Plugin\Field\FieldType\AdjustmentItem $field_item */
    foreach ($this->list as $key => $field_item) {
      if ($field_item->value === $adjustment) {
        $this->removeItem($key);
      }
    }
  }

}
