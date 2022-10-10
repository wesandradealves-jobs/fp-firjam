<?php

namespace Drupal\brazilian_ids\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "brazilian_ids_rg_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "brazilian_ids_rg",
 *   }
 * )
 */
class RgDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
   public static function defaultSettings() {
    return [
      'clean_number' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];
    $element = [
      '#type' => 'brazilian_ids_rg',
      '#title' => $this->fieldDefinition->getLabel(),
      '#description' => $this->fieldDefinition->getDescription(),
      '#number_only' => $this->getFieldSetting('number_only'),
      '#clean_number' => $this->getSetting('clean_number'),
      '#required' => $this->fieldDefinition->isRequired(),
      '#default_value' => $item->toArray(),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['clean_number'] = [
      '#type' => 'checkbox',
      '#title' => t('Clean number'),
      '#description' => t('If checked, the RG number will have dots (.), hyphens (-) and slashes (/) removed after submission.'),
      '#default_value' => $this->getSetting('clean_number'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $clean_number = $this->getSetting('clean_number') ? t('Yes') : t('No');
    $summary[] = t('Clean number: @clean_number', ['@clean_number' => $clean_number]);
    return $summary;
  }

}
