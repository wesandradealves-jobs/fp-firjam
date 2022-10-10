<?php

namespace Drupal\brazilian_ids\Element;

use Drupal\Core\Render\Element\Textfield;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for CPF and CNPJ elements.
 */
class CpfCnpjBase extends Textfield implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * Constructs a Drupal\brazilian_ids\Element\CnpjElement object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler to check if other modules are enabled.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();

    // Changes default input size.
    $info['#size'] = 20;

    // Adds validation callback.
    $info['#element_validate'] = [
      [get_class($this), 'validateElement'],
    ];

    // Adds mask properties to take effect when the Mask Field module (mask)
    // is enabled.
    $mask = $this->getMaskDefaults();
    if ($mask && $this->moduleHandler->moduleExists('mask')) {
      $helper = new \Drupal\mask\Helper\ElementHelper();
      $helper->elementInfoAlter($info, $mask);
    }

    return $info;
  }

  /**
   * Returns mask settings for this element type.
   */
  protected function getMaskDefaults() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('module_handler'));
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $value = parent::valueCallback($element, $input, $form_state);
    return \Drupal::service('brazilian_ids')->clean($value);
  }

  /**
   * Validates the CNPJ value.
   */
  public static function validateElement(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = $element['#value'];
    $error = [];
    if ($value !== '' && !static::validateValue($value, $error)) {
      $form_state->setError($element, $error['message']);
    }
  }

  /**
   * Validates the submitted value.
   *
   * @param string $value
   *   The value of the CPF or CNPJ number to be validated.
   *
   * @param array $error
   *   If provided, it is filled with the error message at $error['message'] if any.
   *
   * @see \Drupal\brazilian_ids\BrazilianIdsService::validateCpfCnpj()
   */
  protected static function validateValue($value, array &$error = []) {
    return \Drupal::service('brazilian_ids')->validateCpfCnpj($value, $error);
  }

}
