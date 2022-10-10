<?php

namespace Drupal\brazilian_ids\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;

/**
 * Field type for RG numbers.
 *
 * @FieldType(
 *   id = "brazilian_ids_rg",
 *   label = @Translation("RG"),
 *   description = @Translation("A field type to collect RG information including number, issuing entity and state."),
 *   default_widget = "brazilian_ids_rg_default",
 *   default_formatter = "brazilian_ids_rg_default"
 * )
 */
class RgItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'number_only' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'number' => [
          'type' => 'varchar',
          'length' => 20,
        ],
      ],
    ];

    if (!$field_definition->getSetting('number_only')) {
      $schema['columns'] += [
        'agency' => [
          'type' => 'varchar',
          'length' => 60,
        ],
        'state' => [
          'type' => 'varchar',
          'length' => 2,
        ],
      ];
    }

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['number'] = DataDefinition::create('string')->setLabel(t('The RG number.'));
    $properties['agency'] = DataDefinition::create('string')->setLabel(t('The issuing agency.'));
    $properties['state'] = DataDefinition::create('string')->setLabel(t('The two-letter state code.'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $number = $this->get('number')->getValue();
    return empty($number);
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element['number_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Number only'),
      '#description' => $this->t('If checked, the issuing agency and state will NOT be collected. This option can not be changed after data has been stored.'),
      '#default_value' => $this->getSetting('number_only'),
      '#disabled' => $has_data,
    ];
    return $element;
  }

}
