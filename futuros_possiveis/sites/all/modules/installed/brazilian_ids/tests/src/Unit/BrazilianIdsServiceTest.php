<?php

namespace Drupal\Tests\brazilian_ids\Unit;

use Drupal\brazilian_ids\BrazilianIdsService;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\brazilian_ids\BrazilianIdsService
 * @group brazilian_ids
 */
class BrazilianIdsServiceTest extends UnitTestCase {

  private $service;

  /**
   * Tests cleaning a CPF numbers.
   */
  public function testCleanCpf() {
    $this->assertEquals('29979245883', $this->service->clean('299.792.458-83'));
  }

  /**
   * Tests cleaning a CNPJ numbers.
   */
  public function testCleanCnpj() {
    $this->assertEquals('27491687000161', $this->service->clean('27.491.687/0001-61'));
  }

  /**
   * Tests cleaning a RG numbers.
   */
  public function testCleanRg() {
    $this->assertEquals('275353369', $this->service->clean('27.535.336-9'));
    $this->assertEquals('27535336X', $this->service->clean('27.535.336-X'));
    $this->assertEquals('27535336x', $this->service->clean('27.535.336-x'));
  }

  /**
   * Tests CPF validation.
   */
  public function testValidateCpf() {
    // Valid CPF numbers.
    $this->assertTrue($this->service->validateCpf('29979245883'));
    $this->assertTrue($this->service->validateCpf('81684990009'));
    $this->assertTrue($this->service->validateCpf('82523167000'));
    $this->assertTrue($this->service->validateCpf('08219249072'));

    // Invalid varification digits.
    $this->assertFalse($this->service->validateCpf('29979245873'));
    $this->assertFalse($this->service->validateCpf('29979245881'));
    $this->assertFalse($this->service->validateCpf('29979245803'));
    $this->assertFalse($this->service->validateCpf('29979245880'));
    $this->assertFalse($this->service->validateCpf('29979245800'));

    // Less than 11 digits.
    $this->assertFalse($this->service->validateCpf('2997924588'));

    // More than 11 digits.
    $this->assertFalse($this->service->validateCpf('299792458833'));

    // CPF numbers using just one digit must be false.
    $this->assertFalse($this->service->validateCpf('00000000000'));
    $this->assertFalse($this->service->validateCpf('11111111111'));
    $this->assertFalse($this->service->validateCpf('22222222222'));
    $this->assertFalse($this->service->validateCpf('33333333333'));
    $this->assertFalse($this->service->validateCpf('44444444444'));
    $this->assertFalse($this->service->validateCpf('55555555555'));
    $this->assertFalse($this->service->validateCpf('66666666666'));
    $this->assertFalse($this->service->validateCpf('77777777777'));
    $this->assertFalse($this->service->validateCpf('88888888888'));
    $this->assertFalse($this->service->validateCpf('99999999999'));
  }

  /**
   * Tests CNPJ validation.
   */
  public function testValidateCnpj() {
    // Valid CNPJ numbers.
    $this->assertTrue($this->service->validateCnpj('45598438000151'));
    $this->assertTrue($this->service->validateCnpj('46264168000105'));
    $this->assertTrue($this->service->validateCnpj('50232337000100'));
    $this->assertTrue($this->service->validateCnpj('09753778000146'));

    // Invalid varification digits.
    $this->assertFalse($this->service->validateCnpj('45598438000161'));
    $this->assertFalse($this->service->validateCnpj('45598438000152'));
    $this->assertFalse($this->service->validateCnpj('45598438000101'));
    $this->assertFalse($this->service->validateCnpj('45598438000150'));
    $this->assertFalse($this->service->validateCnpj('45598438000100'));

    // Less than 14 digits.
    $this->assertFalse($this->service->validateCnpj('4559843800015'));

    // More than 14 digits.
    $this->assertFalse($this->service->validateCnpj('455984380001511'));

    // CNPJ numbers using just one digit must be false.
    $this->assertFalse($this->service->validateCnpj('00000000000000'));
    $this->assertFalse($this->service->validateCnpj('11111111111111'));
    $this->assertFalse($this->service->validateCnpj('22222222222222'));
    $this->assertFalse($this->service->validateCnpj('33333333333333'));
    $this->assertFalse($this->service->validateCnpj('44444444444444'));
    $this->assertFalse($this->service->validateCnpj('55555555555555'));
    $this->assertFalse($this->service->validateCnpj('66666666666666'));
    $this->assertFalse($this->service->validateCnpj('77777777777777'));
    $this->assertFalse($this->service->validateCnpj('88888888888888'));
    $this->assertFalse($this->service->validateCnpj('99999999999999'));
  }

  /**
   * Tests validation of CPF or CNPJ depending on value's length.
   */
  public function testValidateCpfCnpj() {
    // Valid CPF number.
    $this->assertTrue($this->service->validateCpfCnpj('29979245883'));

    // Valid CNPJ number.
    $this->assertTrue($this->service->validateCpfCnpj('45598438000151'));

    // Invalid CPF number.
    $this->assertFalse($this->service->validateCpfCnpj('29979245881'));

    // Invalid CNPJ number.
    $this->assertFalse($this->service->validateCpfCnpj('45598438000150'));

    // Numbers that do not have either 11 or 14 digits.
    $this->assertFalse($this->service->validateCpfCnpj('2997924588'));
    $this->assertFalse($this->service->validateCpfCnpj('299792458833'));
    $this->assertFalse($this->service->validateCpfCnpj('455984380001512'));
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->service = new BrazilianIdsService();
    $string_translation = $this->getStringTranslationStub();
    $this->service->setStringTranslation($string_translation);
  }

}
