<?php

namespace Drupal\brazilian_ids\Element;

use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FormElement("brazilian_ids_rg")
 */
class RgElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#title' => '',
      '#description' => '',
      '#required' => FALSE,
      '#default_value' => [
        'number' => NULL,
        'agency' => NULL,
        'state' => NULL,
      ],
      '#number_only' => FALSE,
      '#clean_number' => FALSE,
      '#state_options' => [
        'AC' => 'AC',
        'AL' => 'AL',
        'AM' => 'AM',
        'AP' => 'AP',
        'BA' => 'BA',
        'CE' => 'CE',
        'DF' => 'DF',
        'ES' => 'ES',
        'GO' => 'GO',
        'MA' => 'MA',
        'MG' => 'MG',
        'MS' => 'MS',
        'MT' => 'MT',
        'PA' => 'PA',
        'PB' => 'PB',
        'PE' => 'PE',
        'PI' => 'PI',
        'PR' => 'PR',
        'RJ' => 'RJ',
        'RN' => 'RN',
        'RO' => 'RO',
        'RR' => 'RR',
        'RS' => 'RS',
        'SC' => 'SC',
        'SE' => 'SE',
        'SP' => 'SP',
        'TO' => 'TO',
      ],
      '#process' => [
        [$class, 'processElement'],
      ],
      '#element_validate' => [
        [$class, 'validateElement'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $value = ($input === FALSE) ? $element['#default_value'] : $input['rg_fields'];
    if (isset($value['number'])) {
      $value['number'] = $element['#clean_number'] ? \Drupal::service('brazilian_ids')->clean($value['number']) : str_replace(' ', '', $value['number']);
    }
    if (isset($value['agency'])) {
      $value['agency'] = trim($value['agency']);
    }
    return $value;
  }

  /**
   * Adds form elements to collect the RG field properties.
   */
  public static function processElement(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $element['rg_fields'] = [
      '#type' => $element['#number_only'] ? 'container' : 'fieldset',
      '#title' => $element['#title'],
      '#description' => $element['#description'],
      '#tree' => TRUE, 
    ];
    $element['rg_fields']['number'] = [
      '#type' => 'textfield',
      '#title' => $element['#number_only'] ? $element['#title'] : t('Number'),
      '#description' => $element['#number_only'] ? $element['#description'] : '',
      '#maxlength' => 20,
      '#size' => 20,
      '#required' => $element['#required'],
      '#default_value' => isset($element['#default_value']['number']) ? $element['#default_value']['number'] : '',
    ];
    if (empty($element['#number_only'])) {
      $element['rg_fields']['agency'] = [
        '#type' => 'textfield',
        '#title' => t('Issuing agency'),
        '#maxlength' => 60,
        '#size' => 20,
        '#required' => $element['#required'],
        '#default_value' => isset($element['#default_value']['agency']) ? $element['#default_value']['agency'] : '',
      ];
      $element['rg_fields']['state'] = [
        '#type' => 'select',
        '#title' => t('State'),
        '#options' => $element['#state_options'],
        '#empty_value' => '',
        '#required' => $element['#required'],
        '#default_value' => isset($element['#default_value']['state']) ? $element['#default_value']['state'] : '',
      ];
    }
    return $element;
  }

  /**
   * Validates the RG element.
   */
  public static function validateElement(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = $element['#value'];

    // If any of the properties are provided, then all of them must be.
    $filled_properties = array_filter($value);
    $empty_properties = array_diff_key($value, $filled_properties);
    if ($filled_properties && $empty_properties) {
      foreach ($empty_properties as $property => $value) {
        $property_element = &$element['rg_fields'][$property];
        $form_state->setError($property_element, t('The @field\'s %property must be provided.', [
          '@field' => $element['#title'],
          '%property' => $property_element['#title'],
        ]));
      }
    }
  }

}
