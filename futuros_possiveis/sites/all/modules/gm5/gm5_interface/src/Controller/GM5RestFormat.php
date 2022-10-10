<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloWorldController.
 */

namespace Drupal\gm5_interface\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * HelloWorldController.
 */
class GM5RestFormat extends ControllerBase {
  /**
   * Generates an example page.
   */
  public function content() {
    $params = \Drupal::request()->request->all();
    print_r($params); die;
  }
}
