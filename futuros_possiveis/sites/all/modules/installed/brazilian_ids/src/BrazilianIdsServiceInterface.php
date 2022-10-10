<?php

namespace Drupal\brazilian_ids;

/**
 * Defines the interface of the Brazilian IDs service.
 */
interface BrazilianIdsServiceInterface {

  /**
   * Cleans up Brazilian IDs numbers.
   */
  public function clean($value);

  /**
   * Validates CPF numbers.
   *
   * @param string $value
   *   The value of the CPF number to be validated.
   *
   * @param array $error
   *   If provided, it is filled with the error message at $error['message'] if any.
   *
   * @return bool
   *   Returns TRUE if the CPF number is valid. Otherwise, returns FALSE.
   */
  public function validateCpf($value, array &$error = []);

  /**
   * Validates CNPJ numbers.
   *
   * @param string $value
   *   The value of the CNPJ number to be validated.
   *
   * @param array $error
   *   If provided, it is filled with the error message at $error['message'] if any.
   *
   * @return bool
   *   Returns TRUE if the CNPJ number is valid. Otherwise, returns FALSE.
   */
  public function validateCnpj($value, array &$error = []);

  /**
   * Validates CPF or CNPJ numbers depending on the number of digits.
   *
   * @param string $value
   *   The value of the CPF or CNPJ number to be validated.
   *
   * @param array $error
   *   If provided, it is filled with the error message at $error['message'] if any.
   *
   * @return bool
   *   Returns TRUE if the CPF or CNPJ number is valid. Otherwise, returns FALSE.
   */
  public function validateCpfCnpj($value, array &$error = []);

  /**
   * Formats a CPF number like 999.999.999-99.
   *
   * @param string $value
   *   The CPF number to be formatted.
   *
   * @return string
   *   The formatted CPF number.
   */
  public function formatCpf($value);

  /**
   * Formats a CNPJ number like 99.999.999/9999-99.
   *
   * @param string $value
   *   The CNPJ number to be formatted.
   *
   * @return string
   *   The formatted CNPJ number.
   */
  public function formatCnpj($value);

  /**
   * Formats a CPF number like 999.999.999-99 or a CNPJ number like 
   * 99.999.999/9999-99 depending on the number of digits.
   *
   * @param string $value
   *   The CPF or CNPJ number to be formatted.
   *
   * @return string
   *   The formatted CPF or CNPJ number.
   */
  public function formatCpfCnpj($value);

}
