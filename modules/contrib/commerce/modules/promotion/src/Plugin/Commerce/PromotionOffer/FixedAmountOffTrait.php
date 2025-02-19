<?php

namespace Drupal\commerce_promotion\Plugin\Commerce\PromotionOffer;

use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_price\Price;

/**
 * Provides common configuration for fixed amount off offers.
 */
trait FixedAmountOffTrait {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'amount' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $amount = $this->configuration['amount'];
    // A bug in the plugin_select form element causes $amount to be incomplete.
    if (isset($amount) && !isset($amount['number'], $amount['currency_code'])) {
      $amount = NULL;
    }

    $form['amount'] = [
      '#type' => 'commerce_price',
      '#title' => $this->t('Amount off'),
      '#default_value' => $amount,
      '#required' => TRUE,
      '#weight' => -1,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['amount'] = $values['amount'];
    }
  }

  /**
   * Gets the offer amount.
   *
   * @return \Drupal\commerce_price\Price|null
   *   The amount, or NULL if unknown.
   */
  protected function getAmount() {
    if (!empty($this->configuration['amount'])) {
      $amount = $this->configuration['amount'];
      return new Price($amount['number'], $amount['currency_code']);
    }
  }

}
