<?php

namespace Drupal\commerce_price\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'commerce_price_default' widget.
 */
#[FieldWidget(
  id: "commerce_price_default",
  label: new TranslatableMarkup("Price"),
  field_types: ["commerce_price"],
)]
class PriceDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['#type'] = 'commerce_price';
    if (!$items[$delta]->isEmpty()) {
      $element['#default_value'] = $items[$delta]->toPrice()->toArray();
    }
    $element['#available_currencies'] = array_filter($this->getFieldSetting('available_currencies'));
    $element['#allow_negative'] = $this->getFieldSetting('allow_negative');

    return $element;
  }

}
