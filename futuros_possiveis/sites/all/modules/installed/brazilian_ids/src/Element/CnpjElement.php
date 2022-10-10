<?php

namespace Drupal\brazilian_ids\Element;

/**
 * @FormElement("brazilian_ids_cnpj")
 */
class CnpjElement extends CpfCnpjBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $info['#maxlength'] = 18;
    return $info;
  }

  /**
   * {@inheritdoc}
   */
  protected function getMaskDefaults() {
    return [
      'value' => '00.000.000/0000-00',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected static function validateValue($value, array &$error = []) {
    return \Drupal::service('brazilian_ids')->validateCnpj($value, $error);
  }

}
