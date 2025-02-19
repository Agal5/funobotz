<?php

namespace Drupal\commerce_store\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\EntityField;

/**
 * Displays the store.
 *
 * Can be configured to show nothing when there's only one possible store.
 *
 * @ingroup views_field_handlers
 */
#[ViewsField("commerce_store")]
class Store extends EntityField {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['hide_single_store'] = ['default' => TRUE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['hide_single_store'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Hide if there's only one store."),
      '#default_value' => $this->options['hide_single_store'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    $store_query = $this->entityTypeManager->getStorage('commerce_store')->getQuery();
    $store_count = $store_query->count()->accessCheck(TRUE)->execute();
    if ($this->options['hide_single_store'] && $store_count <= 1) {
      return FALSE;
    }

    return parent::access($account);
  }

}
