<?php

namespace Drupal\Tests\commerce_tax\Kernel\Plugin\Commerce\TaxType;

use Drupal\Tests\commerce_order\Kernel\OrderKernelTestBase;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\OrderItemType;
use Drupal\commerce_price\Price;
use Drupal\commerce_store\Entity\Store;
use Drupal\commerce_tax\Entity\TaxType;
use Drupal\commerce_tax\TaxableType;
use Drupal\profile\Entity\Profile;

// cspell:ignore Mittelberg Büsingen

/**
 * @coversDefaultClass \Drupal\commerce_tax\Plugin\Commerce\TaxType\EuropeanUnionVat
 * @group commerce
 */
class EuropeanUnionVatTest extends OrderKernelTestBase {

  /**
   * The tax type.
   *
   * @var \Drupal\commerce_tax\Entity\TaxTypeInterface
   */
  protected $taxType;

  /**
   * A sample user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'commerce_tax',
    'commerce_tax_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig('commerce_tax');

    // Order item types that doesn't need a purchasable entity, for simplicity.
    OrderItemType::create([
      'id' => 'test_physical',
      'label' => 'Test (Physical)',
      'orderType' => 'default',
      'third_party_settings' => [
        'commerce_tax' => ['taxable_type' => TaxableType::PHYSICAL_GOODS],
      ],
    ])->save();
    OrderItemType::create([
      'id' => 'test_digital',
      'label' => 'Test (Digital)',
      'orderType' => 'default',
      'third_party_settings' => [
        'commerce_tax' => ['taxable_type' => TaxableType::DIGITAL_GOODS],
      ],
    ])->save();

    $user = $this->createUser();
    $this->user = $this->reloadEntity($user);

    $this->taxType = TaxType::create([
      'id' => 'european_union_vat',
      'label' => 'EU VAT',
      'plugin' => 'european_union_vat',
      'configuration' => [
        'display_inclusive' => TRUE,
      ],
      // Don't allow the tax type to apply automatically.
      'status' => FALSE,
    ]);
    $this->taxType->save();
  }

  /**
   * @covers ::applies
   * @covers ::apply
   */
  public function testApplication() {
    $plugin = $this->taxType->getPlugin();
    // German customer, French store, VAT number provided.
    $order = $this->buildOrder('DE', 'FR', 'DE123456789', ['FR']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|ic|zero', $adjustment->getSourceId());

    // French customer, French store, no tax registration, tax shouldn't apply.
    $order = $this->buildOrder('FR', 'FR');
    $this->assertFalse($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $this->assertCount(0, $adjustments);

    // French customer, French store, VAT number provided.
    $order = $this->buildOrder('FR', 'FR', 'FR00123456789', ['FR']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|fr|standard', $adjustment->getSourceId());

    // German customer, French store, physical product.
    $order = $this->buildOrder('DE', 'FR', '', ['FR']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|fr|standard', $adjustment->getSourceId());

    // GB customer, French store, no VAT.
    $order = $this->buildOrder('GB', 'FR', '', ['FR']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $this->assertCount(0, $adjustments);

    // German customer, French store registered for German VAT, physical product.
    $order = $this->buildOrder('DE', 'FR', '', ['DE']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|de|standard', $adjustment->getSourceId());

    // German customer, French store, digital product before Jan 1st 2015.
    $order = $this->buildOrder('DE', 'FR', '', ['FR'], TRUE);
    $order->setPlacedTime(mktime(1, 1, 1, 1, 1, 2014));
    $order->save();
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|fr|standard', $adjustment->getSourceId());

    // German customer, French store, digital product.
    $order = $this->buildOrder('DE', 'FR', '', ['FR'], TRUE);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|de|standard', $adjustment->getSourceId());

    // German customer, US store registered in FR, digital product.
    $order = $this->buildOrder('DE', 'US', '', ['FR'], TRUE);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|de|standard', $adjustment->getSourceId());

    // German customer with VAT number, US store registered in FR, digital product.
    $order = $this->buildOrder('DE', 'US', 'DE123456789', ['FR'], TRUE);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|ic|zero', $adjustment->getSourceId());

    // Serbian customer, French store, physical product.
    $order = $this->buildOrder('RS', 'FR', '', ['FR']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $this->assertCount(0, $order->collectAdjustments());

    // French customer, Serbian store, physical product.
    $order = $this->buildOrder('FR', 'RS');
    $this->assertFalse($plugin->applies($order));

    // Portuguese customer in Madeira, Portuguese store, physical product.
    $order = $this->buildOrder('PT', 'PT', '', ['PT']);
    $billing_profile = $order->getBillingProfile();
    $billing_profile->set('address', [
      'country_code' => 'PT',
      'postal_code' => '9004-519',
    ]);
    $billing_profile->save();
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|pt_30|standard', $adjustment->getSourceId());

    // French customer in Corsica, FR store, physical product.
    $order = $this->buildOrder('FR', 'FR', '', ['FR']);
    $billing_profile = $order->getBillingProfile();
    $billing_profile->set('address', [
      'country_code' => 'FR',
      'postal_code' => '20090',
    ]);
    $billing_profile->save();
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|fr_h|standard', $adjustment->getSourceId());

    // German customer, AT store registered in AT, physical product.
    $order = $this->buildOrder('DE', 'AT', '', ['AT']);
    $this->assertTrue($plugin->applies($order));
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|at|standard', $adjustment->getSourceId());

    // Austrian customer in Mittelberg, AT store registered in AT,
    // physical product.
    $order = $this->buildOrder('DE', 'AT', '', ['AT']);
    $billing_profile = $order->getBillingProfile();
    $billing_profile->set('address', [
      'country_code' => 'AT',
      'postal_code' => '6991',
    ]);
    $billing_profile->save();
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $adjustment = reset($adjustments);
    $this->assertCount(1, $adjustments);
    $this->assertEquals('european_union_vat|at|standard', $adjustment->getSourceId());

    // German customer in Büsingen, DE store registered in DE,
    // physical product.
    // EU VAT does not apply as customer is in Switzerland for VAT.
    $order = $this->buildOrder('DE', 'DE', '', ['DE']);
    $billing_profile = $order->getBillingProfile();
    $billing_profile->set('address', [
      'country_code' => 'DE',
      'postal_code' => '78266',
    ]);
    $billing_profile->save();
    $plugin->apply($order);
    $adjustments = $order->collectAdjustments();
    $this->assertCount(0, $adjustments);
  }

  /**
   * @covers ::getZones
   */
  public function testGetZones() {
    /** @var \Drupal\commerce_tax\Plugin\Commerce\TaxType\LocalTaxTypeInterface $plugin */
    $plugin = $this->taxType->getPlugin();
    $zones = $plugin->getZones();
    $this->assertArrayHasKey('be', $zones);
    $this->assertArrayHasKey('es', $zones);
    $this->assertArrayHasKey('fr', $zones);
    $this->assertArrayHasKey('it', $zones);
    $this->assertArrayHasKey('de', $zones);
    $germany_tax_rates = $zones['de']->getRates();
    $standard_rate = $germany_tax_rates['standard']->toArray();
    // Ensure the "fake" tax rate percentage added by our test subscriber is present.
    $fake_percentage = end($standard_rate['percentages']);
    $this->assertEquals([
      'number' => '0.25',
      'start_date' => '2041-01-01',
    ], $fake_percentage);
  }

  /**
   * Builds an order for testing purposes.
   *
   * @param string $customer_country
   *   The customer's country code.
   * @param string $store_country
   *   The store's country code.
   * @param string $tax_number
   *   The customer's tax number.
   * @param array $store_registrations
   *   The store's tax registrations.
   * @param bool $digital
   *   Whether the order will be used for a digital product.
   *
   * @return \Drupal\commerce_order\Entity\OrderInterface
   *   The order.
   */
  protected function buildOrder($customer_country, $store_country, $tax_number = '', array $store_registrations = [], $digital = FALSE) {
    $store = Store::create([
      'type' => 'default',
      'label' => 'My store',
      'address' => [
        'country_code' => $store_country,
      ],
      'prices_include_tax' => FALSE,
      'tax_registrations' => $store_registrations,
    ]);
    $store->save();
    $customer_profile = Profile::create([
      'type' => 'customer',
      'uid' => $this->user->id(),
      'address' => [
        'country_code' => $customer_country,
      ],
    ]);
    if ($tax_number) {
      $customer_profile->set('tax_number', [
        'type' => 'european_union_vat',
        'value' => $tax_number,
        'verification_state' => 'success',
      ]);
    }
    $customer_profile->save();
    $order_item = OrderItem::create([
      'type' => $digital ? 'test_digital' : 'test_physical',
      'quantity' => 1,
      // Intentionally uneven number, to test rounding.
      'unit_price' => new Price('10.33', 'USD'),
    ]);
    $order_item->save();
    $order = Order::create([
      'type' => 'default',
      'uid' => $this->user->id(),
      'store_id' => $store,
      'billing_profile' => $customer_profile,
      'order_items' => [$order_item],
    ]);
    $order->save();

    return $order;
  }

}
