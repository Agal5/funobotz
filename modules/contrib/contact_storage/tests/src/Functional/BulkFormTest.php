<?php

namespace Drupal\Tests\contact_storage\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\views\Tests\ViewTestData;

/**
 * Tests a contact message bulk form.
 *
 * @group contact_storage
 * @see \Drupal\contact_storage\Plugin\views\field\MessageBulkForm
 */
class BulkFormTest extends ContactStorageTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to be enabled.
   *
   * @var array
   */
  protected static $modules = [
    'contact_storage',
    'contact_test_views',
    'language',
  ];

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_contact_message_bulk_form'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::Setup();
    // Create and login administrative user.
    $admin_user = $this->drupalCreateUser([
      'administer contact forms',
    ]);
    $this->drupalLogin($admin_user);
    // Create first valid contact form.
    $mail = 'simpletest@example.com';
    $this->addContactForm('test_id', 'test_label', $mail, TRUE);
    $this->assertSession()->pageTextContains('Contact form test_label has been added.');
    $this->drupalLogout();

    // Ensure that anonymous can submit site-wide contact form.
    user_role_grant_permissions(AccountInterface::ANONYMOUS_ROLE, ['access site-wide contact form']);
    $this->drupalGet('contact');
    $this->assertSession()->pageTextContains('Your email address');
    // Submit contact form few times.
    for ($i = 1; $i <= 5; $i++) {
      $this->submitContact($this->randomMachineName(), $mail, $this->randomMachineName(), 'test_id', $this->randomMachineName());
      $this->assertSession()->pageTextContains('Your message has been sent.');
    }
  }

  /**
   * Test multiple deletion.
   */
  public function testBulkDeletion() {
    $this->drupalGet('contact');
    ViewTestData::createTestViews(get_class($this), ['contact_test_views']);
    // Check the operations are accessible to the administer permission user.
    $this->drupalLogin($this->drupalCreateUser(['administer contact forms']));
    $this->drupalGet('test-contact-message-bulk-form');
    // There are 2 options: "- Select -", "Delete contact message".
    $elements = $this->xpath('//select[@id="edit-action"]//option[@value="contact_message_delete_action"]');
    $this->assertCount(1, $elements, 'All contact message operations are found.');
    $this->drupalGet('test-contact-message-bulk-form');
    $this->submitForm([], 'Apply to selected items');
    $this->assertSession()->pageTextContains('No message selected.');
  }

}
