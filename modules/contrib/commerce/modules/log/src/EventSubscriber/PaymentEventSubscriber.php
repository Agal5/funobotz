<?php

namespace Drupal\commerce_log\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_log\LogStorageInterface;
use Drupal\commerce_payment\Event\FailedPaymentEvent;
use Drupal\commerce_payment\Event\PaymentEvent;
use Drupal\commerce_payment\Event\PaymentEvents;
use Drupal\commerce_payment\FailedPaymentDetailsInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\ManualPaymentGatewayInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to the payment events.
 */
class PaymentEventSubscriber implements EventSubscriberInterface {

  /**
   * The log storage.
   */
  protected LogStorageInterface $logStorage;

  /**
   * Constructs a new PaymentEventSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->logStorage = $entity_type_manager->getStorage('commerce_log');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      PaymentEvents::PAYMENT_INSERT => ['onPaymentInsert', -100],
      PaymentEvents::PAYMENT_UPDATE => ['onPaymentUpdate', -100],
      PaymentEvents::PAYMENT_DELETE => ['onPaymentDelete', -100],
      PaymentEvents::PAYMENT_FAILURE => ['onPaymentFailure', -100],
    ];
  }

  /**
   * Creates a log when a payment is added.
   *
   * @param \Drupal\commerce_payment\Event\PaymentEvent $event
   *   The payment event.
   */
  public function onPaymentInsert(PaymentEvent $event): void {
    $payment = $event->getPayment();

    // Skip for payments without order.
    if ($payment->getOrder() === NULL) {
      return;
    }

    $gateway = $payment->getPaymentGateway();
    $state = $payment->getState();
    // Set template based on payment state.
    $template_id = match ($state->getId()) {
      'authorization' => 'payment_authorized',
      'completed' => 'payment_completed',
      default => 'payment_added',
    };
    if ($template_id === 'payment_completed' &&
      ($gateway && $gateway->getPlugin() instanceof ManualPaymentGatewayInterface)) {
      $template_id = 'payment_manual_received';
    }

    $this->logStorage->generate($payment->getOrder(), $template_id, [
      'id' => $payment->id(),
      'remote_id' => $payment->getRemoteId(),
      'amount' => $payment->getAmount(),
      'state' => $state->getLabel(),
      'method' => $payment->getPaymentMethod()?->label(),
      'gateway' => $gateway?->label(),
    ])->save();
  }

  /**
   * Creates a log when a payment is updated.
   *
   * @param \Drupal\commerce_payment\Event\PaymentEvent $event
   *   The payment event.
   */
  public function onPaymentUpdate(PaymentEvent $event): void {
    $payment = $event->getPayment();

    // Skip for payments without order.
    if ($payment->getOrder() === NULL) {
      return;
    }

    $state = $payment->getState();
    $template_id = 'payment_updated';

    // Gets original state if possible.
    $original_state = isset($payment->original) ? $payment->original->getState()
      ->getId() : '';
    $gateway = $payment->getPaymentGateway();

    // For changed state to the authorized use another template.
    if ($state->getId() === 'authorization' && $original_state !== 'authorization') {
      $template_id = 'payment_authorized';
    }

    // For changed state to the completed use another template.
    if ($state->getId() === 'completed' && $original_state !== 'completed') {
      $template_id = 'payment_completed';

      if ($gateway && $gateway->getPlugin() instanceof ManualPaymentGatewayInterface) {
        $template_id = 'payment_manual_received';
      }
    }

    $this->logStorage->generate($payment->getOrder(), $template_id, [
      'id' => $payment->id(),
      'remote_id' => $payment->getRemoteId(),
      'amount' => $payment->getBalance(),
      'state' => $state->getLabel(),
      'method' => $payment->getPaymentMethod()?->label(),
      'gateway' => $gateway?->label(),
    ])->save();
  }

  /**
   * Creates a log when a payment is deleted.
   *
   * @param \Drupal\commerce_payment\Event\PaymentEvent $event
   *   The payment event.
   */
  public function onPaymentDelete(PaymentEvent $event): void {
    $payment = $event->getPayment();

    // Skip for payments without order.
    if ($payment->getOrder() === NULL) {
      return;
    }

    $this->logStorage->generate($payment->getOrder(), 'payment_deleted', [
      'id' => $payment->id(),
      'remote_id' => $payment->getRemoteId(),
      'amount' => $payment->getBalance(),
      'method' => $payment->getPaymentMethod()?->label(),
    ])->save();
  }

  /**
   * Creates a log when payment failed.
   *
   * @param \Drupal\commerce_payment\Event\FailedPaymentEvent $event
   *   The failed payment event.
   */
  public function onPaymentFailure(FailedPaymentEvent $event): void {
    $payment = $event->getPayment();
    $payment_method = $event->getPaymentMethod();

    // Allow payment methods to add additional information to the commerce log.
    // These value will not be used by the log template but can allow contrib
    // and custom code to provide additional reports based on failed payment
    // data.
    if ($payment_method?->getType() instanceof FailedPaymentDetailsInterface) {
      $params = $payment_method->getType()->failedPaymentDetails($payment_method);
    }
    else {
      $params = [];
    }
    $this->logStorage->generate($event->getOrder(), 'payment_failed', [
      'remote_id' => $payment?->getRemoteId(),
      'method' => $payment_method?->label(),
      'error_message' => $event->getGatewayException()->getMessage(),
      'gateway' => $event->getPaymentGateway()->label(),
      'amount' => $payment?->getBalance(),
    ] + $params)->save();
  }

}
