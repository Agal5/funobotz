<?php

namespace Drupal\commerce_product;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\commerce_product\Entity\ProductType;

/**
 * Defines the list builder for products.
 */
class ProductListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Title');
    $header['type'] = $this->t('Type');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\commerce_product\Entity\ProductInterface $entity */
    $product_type = ProductType::load($entity->bundle());

    $row['title']['data'] = Link::fromTextAndUrl($entity->label(), $entity->toUrl());
    $row['type'] = $product_type->label();
    $row['status'] = $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished');

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $variations_url = new Url('entity.commerce_product_variation.collection', [
      'commerce_product' => $entity->id(),
    ]);
    if ($variations_url->access()) {
      $operations['variations'] = [
        'title' => $this->t('Variations'),
        'weight' => 20,
        'url' => $variations_url,
        // Remove the generated destination query parameter, which by default
        // brings the user back to the products listing. This behavior would
        // not make sense on the variations tab (e.g. re-ordering variations
        // should not send the user back to the products listing).
        'query' => ['destination' => NULL],
      ];
    }

    return $operations;
  }

}
