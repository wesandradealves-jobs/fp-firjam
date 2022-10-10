<?php

namespace Drupal\gm5_theme_switch\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

class ThemeNegotiator implements ThemeNegotiatorInterface {

    public function applies(RouteMatchInterface $route_match) {

      $current_path = \Drupal::service('path.current')->getPath();

      if(substr($current_path,1, 5) == 'user/'){
        return 'adminimal_theme';
      } else if($current_path == '/node/86'){
        return 'futurospossiveis2020';
      }
      
    }

    public function determineActiveTheme(RouteMatchInterface $route_match) {
        

      $current_path = \Drupal::service('path.current')->getPath();

      if(substr($current_path,1, 5) == 'usser/'){
        return 'adminimal_theme';
      } else if($current_path == '/node/86'){
        return 'futurospossiveis2020';
      } else {
        return 'futurospossiveis2021';
      }
    }

}
