<?php

namespace Drupal\brazilian_ids\Element;

/**
 * @FormElement("brazilian_ids_cpf")
 */
class CpfElement extends CpfCnpjBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $info['#maxlength'] = 14;
    return $info;
  }

  /**
   * {@inheritdoc}
   */
  protected function getMaskDefaults() {
    return [
      'value' => '000.000.000-00',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected static function validateValue($value, array &$error = []) {
    return \Drupal::service('brazilian_ids')->validateCpf($value, $error);
  }

}
