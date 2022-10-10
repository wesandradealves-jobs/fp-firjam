<?php

namespace Drupal\brazilian_ids\Plugin\Field\FieldFormatter;

use Drupal\brazilian_ids\BrazilianIdsServiceInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formatter for CNPJ numbers.
 *
 * @FieldFormatter(
 *   id = "brazilian_ids_cnpj",
 *   label = @Translation("CNPJ"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class CnpjFormatter extends StringFormatter {

  /**
   * @var \Drupal\brazilian_ids\BrazilianIdsServiceInterface
   */
  private $brazilianIds;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id,
                              $plugin_definition,
                              FieldDefinitionInterface $field_definition,
                              array $settings,
                              $label,
                              $view_mode,
                              array $third_party_settings,
                              EntityManagerInterface $entity_manager,
                              BrazilianIdsServiceInterface $brazilian_ids) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $entity_manager);
    $this->brazilianIds = $brazilian_ids;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id,
                      $plugin_definition,
                      $configuration['field_definition'],
                      $configuration['settings'],
                      $configuration['label'],
                      $configuration['view_mode'],
                      $configuration['third_party_settings'],
                      $container->get('entity.manager'),
                      $container->get('brazilian_ids'));
  }

  /**
   * {@inheritdoc}
   */
  protected function viewValue(FieldItemInterface $item) {
    $render_array = parent::viewValue($item);

    // Uses the formatted value.
    $render_array['#context']['value'] = $this->brazilianIds->formatCnpj($item->value);

    return $render_array;
  }

}
