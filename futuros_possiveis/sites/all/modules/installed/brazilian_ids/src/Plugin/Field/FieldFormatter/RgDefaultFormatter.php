<?php

namespace Drupal\brazilian_ids\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Default formatter for RG fields.
 *
 * @FieldFormatter(
 *   id = "brazilian_ids_rg_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "brazilian_ids_rg"
 *   }
 * )
 */
class RgDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#plain_text' => isset($item->number) ? $item->number : '',
      ];
      if (!empty($item->agency)) {
        $element[$delta]['#plain_text'] .= ' / ' . t('Issuing ag.') . ': ' . $item->agency;
      }
      if (!empty($item->state)) {
        $element[$delta]['#plain_text'] .= ' - ' . $item->state;
      }
    }
    return $element;
  }

}
