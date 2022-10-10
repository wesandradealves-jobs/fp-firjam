<?php

namespace Drupal\brazilian_ids\Element;

use Drupal\Core\Form\FormStateInterface;

/**
 * @FormElement("brazilian_ids_cpf_cnpj")
 */
class CpfCnpjElement extends CpfCnpjBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $info['#maxlength'] = 18;

    // Mask settings are added during element processing to avoid accidental
    // overrides.
    $info['#process'][] = [__CLASS__, 'processMask'];

    return $info;
  }

  /**
   * Processes Form API elements to add mask.
   *
   * @param array $element
   *   The element being processed.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of the form the element belongs to.
   *
   * @param array $complete_form
   *   The form the element belongs to.
   *
   * @return array
   *   The processed element.
   */
  public static function processMask(array &$element, FormStateInterface $form_state, array &$complete_form) {
    if (\Drupal::moduleHandler()->moduleExists('mask')) {
      // The mask is set up by JavaScript.
      $element['#attributes']['class'][] = 'brazilian-ids-cpf-cnpj-mask';
      $element['#attached']['library'][] = 'brazilian_ids/brazilian_ids_mask';
    }
    return $element;
  }

}
