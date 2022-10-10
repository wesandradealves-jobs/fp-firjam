<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloWorldController.
 */

namespace Drupal\gm5_interface\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\views\Views;
use Drupal\node\Entity\Node;
use Drupal\gm5_interface\Plugin\File\GM5File;
use Drupal\gm5_interface\Plugin\Node\GM5Node;
use Drupal\gm5_interface\Plugin\Util\GM5String;
use Drupal\gm5_interface\Plugin\Util\GM5Util;
use Drupal\gm5_interface\Plugin\Views\GM5Views;

/**
 * HelloWorldController.
 */
class GM5InterfaceController extends ControllerBase {
  /**
   * Generates an example page.
   */
  public function content() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Hello world'),
    );
  }
  
  public function duplicados() {
    $viewOficinas = GM5Views::getViewArg('duplicado_de_admin_palestrantes','clear');
    
    $elegiveis = [];
    $removiveis = [];
    foreach($viewOficinas['items'] as $nd){
      $ndvars = $nd->_entity->toArray();

      if(isset($ndvars['field_usuario'][0]['target_id']) && !empty($ndvars['field_usuario'][0]['target_id'])){
        $key = $ndvars['field_usuario'][0]['target_id'].'-'.date('d-m-Y-H',$ndvars['created'][0]['value']);
        
        if(isset($elegiveis[$key])){
          $removiveis[] = $ndvars['nid'][0]['value'];
        } else {
          $elegiveis[$key] = $ndvars['nid'][0]['value'];
        }
      } else {
        $removiveis[] = $ndvars['nid'][0]['value'];
      }
    }
    
    entity_delete_multiple('node',$removiveis);
    print '<strong>'.count($removiveis).' registros removidos! Recarregando página</strong><script>document.location.reload(true);</script>';

    die;
    
    print "<pre>";
    print count($entity_ids);
    print "</pre>";
  }
  
  public function trackingVideo(){
    
    $query = \Drupal::request()->query->all();
    
    if($query['nid']){
      
      $palestra       = \Drupal::entityManager()->getStorage('node')->load($query['nid']);
      $palestraArray  = $palestra->toArray();
      
      $user       = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      $userArray  = $user->toArray();
      
      // print "<pre>";
      // var_dump($palestraArray['field_dia_do_evento'][0]['target_id']);
      // print "</pre>";
      // die;
      
      $node = Node::create([
        'type'        => 'relatorio',
        'title'       => $userArray['field_nome'][0]['value'] . ' - Palestra: ' . $palestraArray['title'][0]['value'] . ' - ' . date('d/m/Y H:i:s'),
        'field_usuario' => ['target_id' => $userArray['uid'][0]['value']],
        'field_dia_do_evento' => ['target_id' => $palestraArray['field_dia_do_evento'][0]['target_id']],
        'field_palestra' => ['target_id' => $query['nid']],
      ]);
      $node->save();
    
    }
  }
}
