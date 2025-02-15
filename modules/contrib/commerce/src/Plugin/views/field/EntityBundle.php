<?php

namespace Drupal\commerce\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\EntityField;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays the entity bundle.
 *
 * Can be configured to show nothing when there's only one possible bundle.
 *
 * @ingroup views_field_handlers
 */
#[ViewsField("commerce_entity_bundle")]
class EntityBundle extends EntityField {

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeBundleInfo = $container->get('entity_type.bundle.info');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['hide_single_bundle'] = ['default' => TRUE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['hide_single_bundle'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Hide if there's only one bundle."),
      '#default_value' => $this->options['hide_single_bundle'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    $bundles = $this->entityTypeBundleInfo->getBundleInfo($this->getEntityType());
    if ($this->options['hide_single_bundle'] && count($bundles) <= 1) {
      return FALSE;
    }

    return parent::access($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getItems(ResultRow $values) {
    $items = parent::getItems($values);
    // Show the bundle label for entity types which don't use a bundle
    // entity type (i.e. they use bundle plugins).
    $entity_type_id = $this->getEntityType();
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    if (!$entity_type->getBundleEntityType()) {
      $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id);
      foreach ($items as &$item) {
        $bundle = $item['raw']->get('value')->getValue();
        if (isset($bundles[$bundle])) {
          $item['rendered']['#context']['value'] = $bundles[$bundle]['label'];
        }
      }
    }

    return $items;
  }

}
