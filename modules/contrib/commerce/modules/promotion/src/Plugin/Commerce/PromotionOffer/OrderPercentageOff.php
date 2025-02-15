<?php

namespace Drupal\commerce_promotion\Plugin\Commerce\PromotionOffer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_promotion\Attribute\CommercePromotionOffer;
use Drupal\commerce_promotion\Entity\PromotionInterface;

/**
 * Provides the percentage off offer for orders.
 *
 * The discount is split between order items, to simplify VAT taxes and refunds.
 */
#[CommercePromotionOffer(
  id: "order_percentage_off",
  label: new TranslatableMarkup("Percentage off the order subtotal"),
  entity_type: "commerce_order"
)]
class OrderPercentageOff extends OrderPromotionOfferBase {

  use PercentageOffTrait;

  /**
   * {@inheritdoc}
   */
  public function apply(EntityInterface $entity, PromotionInterface $promotion) {
    $this->assertEntity($entity);
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $entity;
    $percentage = $this->getPercentage();
    $subtotal_price = $order->getSubtotalPrice();
    if (!$subtotal_price || !$subtotal_price->isPositive()) {
      return;
    }
    // Calculate the order-level discount and split it between order items.
    $amount = $subtotal_price->multiply($percentage);
    $amount = $this->rounder->round($amount);

    $total_price = $order->getTotalPrice();
    if ($total_price && $amount->greaterThan($total_price)) {
      $amount = $total_price;
    }
    // Skip applying the promotion if there's no amount to discount.
    if ($amount->isZero()) {
      return;
    }
    $amounts = $this->splitter->split($order, $amount, $percentage);

    foreach ($order->getItems() as $order_item) {
      if (isset($amounts[$order_item->id()])) {
        $order_item->addAdjustment(new Adjustment([
          'type' => 'promotion',
          'label' => $promotion->getDisplayName() ?: $this->t('Discount'),
          'amount' => $amounts[$order_item->id()]->multiply('-1'),
          'percentage' => $percentage,
          'source_id' => $promotion->id(),
        ]));
      }
    }
  }

}
