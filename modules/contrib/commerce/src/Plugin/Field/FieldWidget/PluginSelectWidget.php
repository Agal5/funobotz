<?php

namespace Drupal\commerce\Plugin\Field\FieldWidget;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\commerce\InlineFormManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'commerce_plugin_select' widget.
 */
#[FieldWidget(
  id: "commerce_plugin_select",
  label: new TranslatableMarkup("Plugin select"),
  field_types: ["commerce_plugin_item"],
)]
class PluginSelectWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The plugin manager for the field's plugin type.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $pluginManager;

  /**
   * The inline form manager.
   *
   * @var \Drupal\commerce\InlineFormManager
   */
  protected $inlineFormManager;

  /**
   * Constructs a new PluginSelectWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   The plugin manager for the field's plugin type.
   * @param \Drupal\commerce\InlineFormManager $inline_form_manager
   *   The inline form manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, PluginManagerInterface $plugin_manager, InlineFormManager $inline_form_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->pluginManager = $plugin_manager;
    $this->inlineFormManager = $inline_form_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    [, $plugin_type] = explode(':', $configuration['field_definition']->getType());

    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('plugin.manager.' . $plugin_type),
      $container->get('plugin.manager.commerce_inline_form')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    [, $plugin_type] = explode(':', $this->fieldDefinition->getType());

    $definitions = $this->pluginManager->getDefinitions();
    $plugins = array_map(static function ($definition) {
      return $definition['label'];
    }, $definitions);
    asort($plugins);
    $target_plugin_id_parents = array_merge($element['#field_parents'], [$items->getName(), $delta, 'target_plugin_id']);
    $target_plugin_id = NestedArray::getValue($form_state->getUserInput(), $target_plugin_id_parents);
    $target_plugin_configuration = [];
    // Fallback to the field value if #ajax hasn't been used yet.
    if ($target_plugin_id === NULL) {
      $target_plugin_id = $items[$delta]->target_plugin_id;
      $target_plugin_configuration = $items[$delta]->target_plugin_configuration ?: [];
    }
    elseif ($target_plugin_id === $items[$delta]->target_plugin_id) {
      $target_plugin_configuration = $items[$delta]->target_plugin_configuration ?: [];
    }
    // The element is required, default to the first plugin.
    if (!$target_plugin_id && $this->fieldDefinition->isRequired()) {
      $plugin_ids = array_keys($plugins);
      $target_plugin_id = reset($plugin_ids);
    }

    $ajax_wrapper_id = Html::getUniqueId('ajax-wrapper');
    $element = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#prefix' => '<div id="' . $ajax_wrapper_id . '">',
      '#suffix' => '</div>',
    ] + $element;
    $element['target_plugin_id'] = [
      '#type' => 'select',
      '#title' => $this->fieldDefinition->getLabel(),
      '#options' => $plugins,
      '#default_value' => $target_plugin_id,
      '#required' => $this->fieldDefinition->isRequired(),
    ];
    if (!$element['target_plugin_id']['#required']) {
      $element['target_plugin_id']['#empty_value'] = '';
    }
    if (self::supportsConfiguration($definitions)) {
      $element['target_plugin_id']['#ajax'] = [
        'callback' => [get_class($this), 'ajaxRefresh'],
        'wrapper' => $ajax_wrapper_id,
      ];

      // Only build the configuration form once a plugin ID has been selected.
      if (!empty($target_plugin_id)) {
        $inline_form = $this->inlineFormManager->createInstance('plugin_configuration', [
          'plugin_type' => $plugin_type,
          'plugin_id' => $target_plugin_id,
          'plugin_configuration' => $target_plugin_configuration,
        ]);
        $element['target_plugin_configuration']['#inline_form'] = $inline_form;
        $element['target_plugin_configuration']['#parents'] = array_merge($element['#field_parents'], [$items->getName(), $delta, 'target_plugin_configuration']);
        $element['target_plugin_configuration'] = $inline_form->buildInlineForm($element['target_plugin_configuration'], $form_state);
      }
    }

    return $element;
  }

  /**
   * Determines whether plugin configuration is supported.
   *
   * Supported if the plugins implement PluginFormInterface.
   *
   * @param array $definitions
   *   The available plugin definitions.
   *
   * @return bool
   *   TRUE if plugin configuration is supported, FALSE otherwise.
   */
  protected static function supportsConfiguration(array $definitions) {
    // The plugin manager has $this->pluginInterface, but there's no getter
    // for it, so it can't be used to check for PluginFormInterface.
    // Instead, we assume that all plugins implement the same interfaces,
    // and perform the check against the first plugin.
    $definition = reset($definitions);
    return is_subclass_of($definition['class'], PluginFormInterface::class);
  }

  /**
   * Ajax callback.
   */
  public static function ajaxRefresh(&$form, FormStateInterface $form_state) {
    $element_parents = array_slice($form_state->getTriggeringElement()['#array_parents'], 0, -1);
    return NestedArray::getValue($form, $element_parents);
  }

}
