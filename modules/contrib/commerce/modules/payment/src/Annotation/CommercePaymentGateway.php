<?php

namespace Drupal\commerce_payment\Annotation;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\commerce_payment\CreditCard;

/**
 * Defines the payment gateway plugin annotation object.
 *
 * Plugin namespace: Plugin\Commerce\PaymentGateway.
 *
 * @see plugin_api
 *
 * @Annotation
 */
class CommercePaymentGateway extends Plugin {

  use StringTranslationTrait;

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The payment gateway label.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The payment gateway display label.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $display_label;

  /**
   * The supported modes.
   *
   * An array of mode labels keyed by machine name.
   *
   * @var string[]
   */
  public $modes;

  /**
   * The payment gateway forms.
   *
   * An array of form classes keyed by operation.
   * For example:
   * <code>
   *   'add-payment-method' => "Drupal\commerce_payment\PluginForm\PaymentMethodAddForm",
   *   'capture-payment' => "Drupal\commerce_payment\PluginForm\PaymentCaptureForm",
   * </code>
   *
   * @var array
   */
  public $forms = [];

  /**
   * The JS library ID.
   *
   * @var string
   *
   * @deprecated in commerce:3.0.0 and is removed from commerce:4.0.0. Use ::libraries instead.
   * @see https://www.drupal.org/project/commerce/issues/3465875
   */
  public $js_library;

  /**
   * The list of library IDs to be attached.
   *
   * @var array
   */
  public $libraries = [];

  /**
   * The payment type used by the payment gateway.
   *
   * @var string
   */
  public $payment_type = 'payment_default';

  /**
   * The payment method types handled by the payment gateway.
   *
   * @var string[]
   */
  public $payment_method_types = [];

  /**
   * The default payment method type.
   *
   * Defaults to the first payment method type if no value is provided.
   *
   * @var string
   */
  public $default_payment_method_type;

  /**
   * The credit card types handled by the payment gateway.
   *
   * @var string[]
   */
  public $credit_card_types = [];

  /**
   * Whether the payment gateway requires billing information to be collected.
   *
   * Defaults to TRUE because prior to Commerce 2.14 payment gateways could
   * assume that billing information is always collected.
   *
   * @var bool
   */
  public $requires_billing_information = TRUE;

  /**
   * Constructs a new CommercePaymentGateway object.
   *
   * @param array $values
   *   The annotation values.
   */
  public function __construct(array $values) {
    if (empty($values['modes'])) {
      $values['modes'] = [
        'test' => $this->t('Test'),
        'live' => $this->t('Live'),
      ];
    }
    if (empty($values['payment_method_types'])) {
      // NestedArray merging causes duplicates for array defaults on properties.
      $values['payment_method_types'] = ['credit_card'];
    }
    if (empty($values['default_payment_method_type'])) {
      $values['default_payment_method_type'] = reset($values['payment_method_types']);
    }
    if (empty($values['credit_card_types'])) {
      $values['credit_card_types'] = array_keys(CreditCard::getTypes());
    }
    parent::__construct($values);
  }

}
