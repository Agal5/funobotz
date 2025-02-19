<?php

namespace Drupal\Tests\contact_storage\Functional;

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Test\AssertMailTrait;
use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\Traits\Core\PathAliasTestTrait;

/**
 * Tests storing contact messages and viewing them through UI.
 *
 * @group contact_storage
 */
class ContactStorageTest extends ContactStorageTestBase {

  use FieldUiTestTrait;
  use AssertMailTrait {
    getMails as drupalGetMails;
  }
  use PathAliasTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * An administrative user with permission to administer contact forms.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'text',
    'block',
    'contact',
    'language',
    'field_ui',
    'contact_test',
    'contact_storage',
    'filter',
  ];

  protected function setUp(): void {
    parent::setUp();

    $this->drupalPlaceBlock('system_breadcrumb_block');
    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalPlaceBlock('page_title_block');

    $full_html_format = FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
    ]);
    $full_html_format->save();

    // Create and login administrative user.
    $this->adminUser = $this->drupalCreateUser([
      'access site-wide contact form',
      'administer contact forms',
      'administer users',
      'administer account settings',
      'administer contact_message fields',
      'administer contact_message form display',
      'administer contact_message display',
      'use text format full_html',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests contact messages submitted through contact form.
   */
  public function testContactStorage() {
    // Create first valid contact form.
    $mail = 'simpletest@example.com';
    $this->addContactForm('test_id', 'test_label', $mail, TRUE);
    $this->assertSession()->pageTextContains('Contact form test_label has been added.');

    // Ensure that new contact form can be cloned.
    $this->cloneContactForm('test_id', 'test_id_cloned');
    $original_form = ContactForm::load('test_id');
    $cloned_form = ContactForm::load('test_id_cloned');
    $this->assertNotEquals($original_form->uuid(), $cloned_form->uuid());

    // Ensure that anonymous can submit site-wide contact form.
    user_role_grant_permissions(AccountInterface::ANONYMOUS_ROLE, ['access site-wide contact form']);
    $this->drupalLogout();
    $this->drupalGet('contact');
    $this->assertSession()->pageTextContains('Your email address');
    $this->assertSession()->pageTextNotContains('Form');
    $this->submitContact('Test_name', $mail, 'Test_subject', 'test_id', 'Test_message');
    $this->assertSession()->pageTextContains('Your message has been sent.');

    // Verify that only 1 message has been sent (by default, the "Send a copy
    // to yourself" option is disabled.
    $captured_emails = $this->drupalGetMails();
    $this->assertCount(1, $captured_emails);

    // Login as admin.
    $this->drupalLogin($this->adminUser);

    // Verify that the global setting stating whether e-mails should be sent in
    // HTML format is false by default.
    $this->assertFalse(\Drupal::config('contact_storage.settings')->get('send_html'));

    // Verify that this first e-mail was sent in plain text format.
    $captured_emails = $this->drupalGetMails();
    $this->assertTrue(strpos($captured_emails[0]['headers']['Content-Type'], 'text/plain') !== FALSE);

    // Go to the settings form and enable sending messages in HTML format.
    $this->drupalGet('/admin/structure/contact/settings');
    $enable_html = [
      'send_html' => TRUE,
    ];
    $this->submitForm($enable_html, 'Save configuration');

    // Check that the form POST was successful.
    $this->assertSession()->pageTextContains('The configuration options have been saved.');

    // Check that the global setting is properly updated.
    $this->assertTrue(\Drupal::config('contact_storage.settings')->get('send_html'));

    $display_fields = [
      "The sender's name",
      "The sender's email",
      "Subject",
    ];

    // Check that the page title is correct.
    $this->drupalGet('contact/test_id');
    $this->assertTrue(!empty($this->cssSelect('h1:contains(test_label)')));
    $this->assertSession()->titleEquals('test_label | Drupal');

    // Check that the configuration edit page title is correct.
    $this->drupalGet('admin/structure/contact/manage/test_id');
    $this->assertTrue(!empty($this->cssSelect('h1:contains(test_label)')));
    $this->assertSession()->titleEquals('Edit test_label | Drupal');

    // Check that name, subject and mail are configurable on display.
    $this->drupalGet('admin/structure/contact/manage/test_id/display');
    foreach ($display_fields as $label) {
      $this->assertSession()->pageTextContains($label);
    }

    // Check that preview is configurable on form display.
    $this->drupalGet('admin/structure/contact/manage/test_id/form-display');
    $this->assertSession()->pageTextContains('Preview');

    // Check the message list overview.
    $this->drupalGet('admin/structure/contact/messages');
    $rows = $this->xpath('//tbody/tr');
    // Make sure only 1 message is available.
    $this->assertCount(1, $rows);
    // Some fields should be present.
    $this->assertSession()->pageTextContains('Test_subject');
    $this->assertSession()->pageTextContains('Test_name');
    $this->assertSession()->pageTextContains('test_label');

    // Click the view link and make sure name, subject and email are displayed
    // by default.
    $this->clickLink('View');
    foreach ($display_fields as $label) {
      $this->assertSession()->pageTextContains($label);
    }

    // Make sure the stored message is correct.
    $this->drupalGet('admin/structure/contact/messages');
    $this->clickLink('Edit');
    $this->assertSession()->fieldValueEquals('edit-name', 'Test_name');
    $this->assertSession()->fieldValueEquals('edit-mail', $mail);
    $this->assertSession()->fieldValueEquals('edit-subject-0-value', 'Test_subject');
    $this->assertSession()->fieldValueEquals('edit-message-0-value', 'Test_message');
    // Submit should redirect back to listing.
    $this->submitForm([], 'Save');
    $this->assertSession()->addressEquals('admin/structure/contact/messages');

    // Delete the message.
    $this->clickLink('Delete');
    $this->submitForm([], 'Delete');
    $this->assertSession()->responseContains((string) t('The @entity-type %label has been deleted.', [
      // See \Drupal\Core\Entity\EntityDeleteFormTrait::getDeletionMessage().
      '@entity-type' => 'contact message',
      '%label'       => 'Test_subject',
    ]));
    // Make sure no messages are available.
    $this->assertSession()->pageTextContains('There is no Contact message yet.');

    // Fill the "Submit button text" field and assert the form can still be
    // submitted.
    $edit = [
      'contact_storage_submit_text' => 'Submit the form',
      'contact_storage_preview' => FALSE,
    ];
    $this->drupalGet('admin/structure/contact/manage/test_id');
    $this->submitForm($edit, 'Save');
    $edit = [
      'subject[0][value]' => 'Test subject',
      'message[0][value]' => 'Test message',
    ];
    $this->drupalGet('contact');
    $element = $this->cssSelect('#edit-preview');
    // Preview button is hidden.
    $this->assertTrue(empty($element));
    $this->submitForm($edit, 'Submit the form');
    $this->assertSession()->pageTextContains('Your message has been sent.');

    // Add an Options email item field to the form.
    // There is a problem with allowed value. I was not able to create field
    // manually on D10.
    $settings = ['settings[allowed_values]' => "test_key1|test_label1|simpletest1@example.com\ntest_key2|test_label2|simpletest2@example.com"];
    $this->fieldUIAddNewField('admin/structure/contact/manage/test_id', 'category', 'Category', 'contact_storage_options_email', $settings);
    // Verify that the new field shows up correctly on the form.
    $this->drupalGet('contact');
    $this->assertSession()->pageTextContains('Category');
    $this->assertSession()->optionExists('edit-field-category', '_none');
    $this->assertSession()->optionExists('edit-field-category', 'test_key1');
    $this->assertSession()->optionExists('edit-field-category', 'test_key2');

    // Send a message using the Options email item field and enable the "Send a
    // copy to yourself" option.
    $captured_emails = $this->drupalGetMails();
    $emails_count_before = count($captured_emails);
    $edit = [
      'subject[0][value]' => 'Test subject',
      'message[0][value]' => 'Test message',
      'field_category' => 'test_key2',
      'copy' => 'checked',
    ];
    $this->submitForm($edit, 'Submit the form');
    $this->assertSession()->pageTextContains('Your message has been sent.');

    // Check that 2 messages were sent and that the body of the last
    // message contains the added message.
    $captured_emails = $this->drupalGetMails();
    $emails_count_after = count($captured_emails);
    $this->assertTrue($emails_count_after === ($emails_count_before + 2));
    $this->assertMailString('body', 'test_key2', 2);

    // The last message is the one sent as a copy, the one before it is the
    // original. Check that the original contains the added recipients and that
    // the copied one is only sent to the sender.
    $logged_in_user_email = $this->loggedInUser->getEmail();
    $this->assertTrue($captured_emails[$emails_count_after - 2]['to'] == "$mail,simpletest2@example.com");
    $this->assertTrue($captured_emails[$emails_count_after - 1]['to'] == $logged_in_user_email);

    // Test clone functionality - add field to existing form.
    $this->fieldUIAddNewField('admin/structure/contact/manage/test_id', 'text_field', 'Text field', 'text');
    // Then clone it.
    $this->cloneContactForm('test_id', 'test_id_2');

    $edit = [
      'subject[0][value]' => 'Test subject',
      'message[0][value]' => 'Test message',
    ];

    // The added field should be on the cloned form too.
    $edit['field_text_field[0][value]'] = 'Some text';
    $this->drupalGet('contact/test_id_2');
    $this->submitForm($edit, 'Submit the form');
    $form = ContactForm::load('test_id_2');
    $this->assertNotEmpty($form->uuid());

    // Try changing the options email label, field default value and setting it
    // to required.
    $this->drupalGet('/admin/structure/contact/manage/test_id/fields');
    $this->clickLink('Edit');
    $this->submitForm([
      'label' => 'Category-2',
      'required' => TRUE,
      'default_value_input[field_category]' => 'test_key1',
    ], 'Save settings');

    // Verify that the changes are visible into the contact form.
    $this->drupalGet('contact');
    $this->assertSession()->pageTextContains('Category-2');
    $this->assertSession()->optionExists('edit-field-category', 'test_key1');
    $this->assertSession()->optionExists('edit-field-category', 'test_key2');
    $this->assertNotEmpty($this->xpath('//select[@id="edit-field-category" and @required="required"]//option[@value="test_key1"]'));

    // Verify that the 'View messages' link exists for the 2 forms and that it
    // links to the correct view.
    $this->drupalGet('/admin/structure/contact');
    $this->assertSession()->linkByHrefExists('/admin/structure/contact/messages?form=test_id');
    $this->assertSession()->linkByHrefExists('/admin/structure/contact/messages?form=test_id_2');

    // Create a new contact form and assert that the disable link exists for
    // each forms.
    $this->addContactForm('test_disable_id', 'test_disable_label', 'simpletest@example.com', FALSE);
    $this->drupalGet('/admin/structure/contact');
    $contact_form_count = count(ContactForm::loadMultiple());
    // In D10 stark theme there are no li.disable, li.enable elements.
    $this->assertCount($contact_form_count, $this->cssSelect('li a[href*="/disable"]:contains(Disable)'));
    $this->drupalGet('/admin/structure/contact/manage/test_disable_id/disable');

    // Disable the form and assert that there is 1 less "Disable" button and 1
    // "Enable" button.
    $this->submitForm([], 'Disable');
    $this->assertSession()->pageTextContains('Disabled contact form test_disable_label.');
    $this->drupalGet('/admin/structure/contact');
    $this->assertCount(($contact_form_count - 1), $this->cssSelect('li a[href*="/disable"]:contains(Disable)'));
    $this->assertCount(1, $this->cssSelect('li a[href*="/enable"]:contains(Enable)'));

    // Assert that the disabled form has no input or text area and the message.
    $this->drupalGet('contact/test_disable_id');
    $this->assertCount(0, $this->cssSelect('input'));
    $this->assertCount(0, $this->cssSelect('textarea'));
    $this->assertSession()->pageTextContains('This contact form has been disabled.');
    $this->drupalGet('/admin/structure/contact/manage/test_disable_id/enable');

    // Try to re-enable the form and assert that it can be accessed.
    $this->submitForm([], 'Enable');
    $this->assertSession()->pageTextContains('Enabled contact form test_disable_label.');
    $this->drupalGet('contact/test_disable_id');
    $this->assertSession()->pageTextNotContains('This contact form has been disabled.');

    // Create a new contact form with a custom disabled message, disable it and
    // assert that the message displayed is correct.
    $this->addContactForm('test_disable_id_2', 'test_disable_label_2', 'simpletest@example.com', FALSE, ['contact_storage_disabled_form_message' => 'custom disabled message']);
    $this->drupalGet('/admin/structure/contact/manage/test_disable_id_2/disable');
    $this->submitForm([], 'Disable');
    $this->assertSession()->pageTextContains('Disabled contact form test_disable_label_2.');
    $this->drupalGet('contact/test_disable_id_2');
    $this->assertSession()->pageTextContains('custom disabled message');
  }

  /**
   * Tests the url alias creation feature.
   */
  public function testUrlAlias() {

    // Add a second language to make sure aliases work with any language.
    $language = ConfigurableLanguage::createFromLangcode('de');
    $language->save();

    // Set the second language as default.
    $this->config('system.site')->set('default_langcode', $language->getId())->save();
    $this->rebuildContainer();

    $mail = 'simpletest@example.com';
    // Test for alias without slash.
    $this->addContactForm('form_alias_1', 'contactForm', $mail, FALSE, ['contact_storage_url_alias' => 'form51']);
    $this->assertSession()->pageTextContains('The alias path has to start with a slash.');
    $this->drupalGet('form51');
    $this->assertSession()->statusCodeEquals(404);

    // Test for correct alias. Verify that we land on the correct contact form.
    $this->addContactForm('form_alias_2', 'contactForm', $mail, FALSE, ['contact_storage_url_alias' => '/form51']);
    $this->assertSession()->pageTextContains('Contact form contactForm has been added.');
    $this->drupalGet('form51');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('contactForm');
    $this->assertSession()->buttonExists('Send message');
    $this->drupalGet('admin/structure/contact/manage/form_alias_2');

    // Edit the contact form without changing anything. Verify that the existing
    // alias continues to work.
    $this->submitForm([], 'Save');
    $this->assertSession()->pageTextContains('Contact form contactForm has been updated.');
    $this->drupalGet('form51');
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('admin/structure/contact/manage/form_alias_2');

    // Edit the contact form by changing the alias. Verify that the new alias
    // is generated and the old one removed.
    $this->submitForm(['contact_storage_url_alias' => '/form52'], 'Save');
    $this->assertSession()->pageTextContains('Contact form contactForm has been updated.');
    $this->drupalGet('form51');
    $this->assertSession()->statusCodeEquals(404);
    $this->drupalGet('form52');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('contactForm');
    $this->drupalGet('admin/structure/contact/manage/form_alias_2');

    // Edit the contact form by removing the alias. Verify that is is deleted.
    $this->submitForm(['contact_storage_url_alias' => ''], 'Save');
    $this->assertSession()->pageTextContains('Contact form contactForm has been updated.');
    $this->drupalGet('form52');
    $this->assertSession()->statusCodeEquals(404);
    $this->drupalGet('admin/structure/contact/manage/form_alias_2');

    // Add an alias back and delete the contact form. Verify that the alias is
    // deleted along with the contact form.
    $this->submitForm(['contact_storage_url_alias' => '/form52'], 'Save');
    $this->assertSession()->pageTextContains('Contact form contactForm has been updated.');
    $this->drupalGet('form52');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('contactForm');
    $this->drupalGet('admin/structure/contact/manage/form_alias_2/delete');
    $this->submitForm([], 'Delete');
    $alias = $this->loadPathAliasByConditions([
      'path' => '/contact/form_alias_2',
    ]);
    $this->assertNull($alias);
  }

  public function testMaximumSubmissionLimit() {
    // Create a new contact form with a maximum submission limit of 2.
    $this->addContactForm('test_id_3', 'test_label', 'simpletest@example.com', FALSE, ['contact_storage_maximum_submissions_user' => 2]);
    $this->assertSession()->pageTextContains('Contact form test_label has been added.');

    // Sends 2 messages with "Send yourself a copy" option activated, shouldn't
    // reach the limit even if 2 messages are sent twice.
    $this->drupalGet('contact/test_id_3');
    $edit = [
      'subject[0][value]' => 'Test subject',
      'message[0][value]' => 'Test message',
      'copy' => 'checked',
    ];
    $this->submitForm($edit, 'Send message');
    $this->assertSession()->pageTextContains('Your message has been sent.');
    $this->drupalGet('contact/test_id_3');
    $this->submitForm($edit, 'Send message');
    $this->assertSession()->pageTextContains('Your message has been sent.');

    // Try accessing the form after the limit has been reached.
    $this->drupalGet('contact/test_id_3');
    $this->assertSession()->pageTextContains('You have reached the maximum submission limit of 2 for this form.');
  }

  /**
   * Tests the Auto-reply field.
   */
  public function testAutoReplyField() {
    // Create a new contact form with an auto-reply.
    $this->addContactForm('test_auto_reply_id_1', 'test_auto_reply_label_1', 'simpletest@example.com', TRUE, ['reply[value]' => "auto_reply_1\nsecond_line"]);
    $this->assertSession()->pageTextContains('Contact form test_auto_reply_label_1 has been added.');

    // Verify that the auto-reply shows up in the field and only offers
    // one format (plain text), since html e-mails are disabled.
    $this->drupalGet('admin/structure/contact/manage/test_auto_reply_id_1');
    $this->assertNotEmpty($this->xpath('//textarea[@id="edit-reply-value" and text()=:text]', [':text' => "auto_reply_1\nsecond_line"]));
    $this->assertEmpty($this->xpath('//select[@name="reply[format]"]'));

    $this->drupalGet('contact');
    $edit = [
      'subject[0][value]' => 'Test subject',
      'message[0][value]' => 'Test message',
    ];
    $this->drupalGet('contact');
    $this->submitForm($edit, 'Send message');
    $this->assertSession()->pageTextContains('Your message has been sent.');

    $captured_emails = $this->drupalGetMails();

    // Checks that the last captured email is the auto-reply, has a correct
    // body and is in html format.
    $this->assertEquals('page_autoreply', end($captured_emails)['key']);
    $this->assertStringContainsString("auto_reply_1\nsecond_line", end($captured_emails)['body']);
    $this->assertStringContainsString('text/plain', end($captured_emails)['headers']['Content-Type']);
    $this->drupalGet('/admin/structure/contact/settings');

    // Enable sending messages in html format and verify that the available
    // formats correctly show up on the contact form edit page.
    $this->submitForm(['send_html' => TRUE], 'Save configuration');
    $this->drupalGet('admin/structure/contact/manage/test_auto_reply_id_1');
    $this->assertNotEmpty($this->xpath('//select[@name="reply[format]"]//option[@value="plain_text" and @selected="selected"]'));
    $this->assertNotEmpty($this->xpath('//select[@name="reply[format]"]//option[@value="full_html"]'));

    // Use custom testing mail system to support HTML mails.
    $mail_config = $this->config('system.mail');
    $mail_config->set('interface.default', 'test_contact_storage_html_mail');
    $mail_config->save();

    // Test sending a HTML mail.
    $this->drupalGet('contact');
    $edit = [
      'subject[0][value]' => 'Test subject',
      'message[0][value]' => 'Test message',
    ];
    $this->drupalGet('contact');
    $this->submitForm($edit, 'Send message');
    $this->assertSession()->pageTextContains('Your message has been sent.');

    $captured_emails = $this->drupalGetMails();
    $this->assertEquals('page_autoreply', end($captured_emails)['key']);
    // 10.1 - "auto_reply_1<br />\nsecond_line"
    // 10.2 - "auto_reply_1<br>\nsecond_line"
    $body = end($captured_emails)['body'];
    $this->assertTrue(strpos($body, "auto_reply_1<br") !== FALSE);
    $this->assertTrue(strpos($body, ">\nsecond_line") !== FALSE);
    $this->assertEquals('text/html', end($captured_emails)['headers']['Content-Type']);
    $this->drupalGet('admin/structure/contact/manage/test_auto_reply_id_1');

    // Select full html format (not selected by default) and verify that it is
    // properly set.
    $this->submitForm(['reply[format]' => 'full_html'], 'Save');
    $this->drupalGet('admin/structure/contact/manage/test_auto_reply_id_1');
    $this->assertNotEmpty($this->xpath('//select[@name="reply[format]"]//option[@value="full_html" and @selected="selected"]'));

  }

  /**
   * Clone form.
   *
   * @param string $original_form_id
   *   The original form machine name.
   * @param string $clone_form_id
   *   The new form machine name.
   */
  protected function cloneContactForm($original_form_id, $clone_form_id) {
    $this->drupalGet("admin/structure/contact/manage/$original_form_id/clone");
    $this->submitForm([
      'id' => $clone_form_id,
      'label' => 'Cloned',
    ], 'Clone');
  }

}
