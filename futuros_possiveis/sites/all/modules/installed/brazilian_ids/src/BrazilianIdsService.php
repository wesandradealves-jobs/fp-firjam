<?php

namespace Drupal\brazilian_ids;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides validation functionalities for Brazilian IDs numbers.
 */
class BrazilianIdsService implements BrazilianIdsServiceInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function clean($value) {
    return isset($value) ? str_replace([' ', '.', '-', '/'], '', $value) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function validateCpf($value, array &$error = []) {
    $is_valid = FALSE;

    if (isset($value) && $value !== '') {
      $forbidden = array(
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999',
      );

      // Checks if the value has the correct number of digits and is none of 
      // the forbidden values.
      if (!preg_match('/^[0-9]{11}$/', $value)) {
        $error['message'] = $this->t('CPF must be a 11-digit number.');
      }
      elseif (in_array($value, $forbidden)) {
        $error['message'] = $this->t('CPF cannot be a sequence of the same digit only.');
      }
      else {
        // Checks the verification digits.
        $valid_number = substr($value, 0, 9);
        for ($computed_digits = 0; $computed_digits < 2; $computed_digits++) {
          $sum = 0;
          $n = 11 + $computed_digits;
          for ($i = 0; $i < 9 + $computed_digits; $i++) {
            $sum += --$n * substr($valid_number, $i, 1);
          }
          $reminder = $sum % 11;
          $valid_number .= ($reminder < 2) ? 0 : 11 - $reminder;
        }
        if (!($is_valid = $valid_number == $value)) {
          $error['message'] = $this->t('The number %value is not a valid CPF number.', ['%value' => $value]);
        }
      }
    }

    return $is_valid;
  }

  /**
   * {@inheritdoc}
   */
  public function validateCnpj($value, array &$error = []) {
    $is_valid = FALSE;

    if (isset($value) && $value !== '') {
      $forbidden = array(
        '00000000000000',
        '11111111111111',
        '22222222222222',
        '33333333333333',
        '44444444444444',
        '55555555555555',
        '66666666666666',
        '77777777777777',
        '88888888888888',
        '99999999999999',
      );

      // Checks if the value has the correct number of digits and is none of 
      // the forbidden values.
      if (!preg_match('/^[0-9]{14}$/', $value)) {
        $error['message'] = $this->t('CNPJ must be a 14-digit number.');
      }
      elseif (in_array($value, $forbidden)) {
        $error['message'] = $this->t('CNPJ cannot be a sequence of the same digit only.');
      }
      else {
        // Checks the verification digits.
        $k = 6;
        $sum1 = 0;
        $sum2 = 0;
        for ($i = 0; $i < 13; $i++) {
          $k = $k == 1 ? 9 : $k;
          $sum2 += $value[$i] * $k--;
          if ($i < 12) {
            $sum1 += ($k == 1) ? $value[$i] * 9 : $value[$i] * $k;
          }
        }
        $digit1 = ($sum1 % 11 < 2) ? 0 : 11 - $sum1 % 11;
        $digit2 = ($sum2 % 11 < 2) ? 0 : 11 - $sum2 % 11;

        if (!($is_valid = $value[12] == $digit1 && $value[13] == $digit2)) {
          $error['message'] = $this->t('The number %value is not a valid CNPJ number.', ['%value' => $value]);
        }
      }
    }

    return $is_valid;
  }

  /**
   * {@inheritdoc}
   */
  public function validateCpfCnpj($value, array &$error = []) {
    $is_valid = FALSE;
    if (isset($value) && $value !== '') {
      // Validates as CPF or CNPJ depending on the number of digits.
      if (preg_match('/^[0-9]{11}$/', $value)) {
        // CPF number.
        $is_valid = $this->validateCpf($value, $error);
      }
      elseif (preg_match('/^[0-9]{14}$/', $value)) {
        // CNPJ number.
        $is_valid = $this->validateCnpj($value, $error);
      }
      else {
        // Value does not match any of the allowed formats.
        $error['message'] = $this->t('The number %value does not match a CPF or a CNPJ number.', ['%value' => $value]);
      }
    }
    return $is_valid;
  }

  /**
   * {@inheritdoc}
   */
  public function formatCpf($value) {
    if (preg_match('/^[0-9]{11}$/', $value)) {
      $value = vsprintf('%s%s%s.%s%s%s.%s%s%s-%s%s', str_split($value));
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function formatCnpj($value) {
    if (preg_match('/^[0-9]{14}$/', $value)) {
      $value = vsprintf('%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s', str_split($value));
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function formatCpfCnpj($value) {
    // Formats as CPF or CNPJ depending on the number of digits.
    if (preg_match('/^[0-9]{11}$/', $value)) {
      // CPF number.
      $value = $this->formatCpf($value);
    }
    elseif (preg_match('/^[0-9]{14}$/', $value)) {
      // CNPJ number.
      $value = $this->formatCnpj($value);
    }
    return $value;
  }

}
