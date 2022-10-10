<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace Drupal\gm5_interface\Plugin\Views;

use Drupal\views\Views;

class GM5Views extends Views {

  /**
   * Variável $option deve ser um objeto e pode passar os seguintes valores:
   * 
   * @param type $name
   * @param type $display
   * @param type $options - Exemplo:
   *                           $options = new StdClass;
   *                           $options->args = array();
   *                           $options->filters = isset($_GET['teste']) ? array('title'=>$_GET['teste']) : array();
   *                           $options->pager = array('autoPager' => TRUE); 
   * @return type
   */
  public static function getViewArg($name,$display,$options = array()){
    $result = array();
    
    $view = SELF::getView($name);
    $view->setDisplay($display);
    
    //Passa argumentos
    if (isset($options->args) && is_array($options->args) && !empty($options->args)) {
      $view->setArguments($options->args);
    }
    
    //Passa filtros expostos
    if (isset($options->filters) && is_array($options->filters) && !empty($options->filters)) {
      $view->setExposedInput($options->filters);
    }
    
    //Seta página atual a partir da variável page, que pode ser passado por POST ou Querystring
    if($view->usePager() && isset($options->pager) && $options->pager['autoPager']){
      if(isset($_REQUEST['page'])){
        $view->setCurrentPage($_REQUEST['page']);
      }
    }
    
    $view->preExecute();
    $view->execute();
    
    $result['filters'] = $view->display_handler->getPlugin('exposed_form')->renderExposedForm();
    $result['totalItems'] = $view->total_rows;
    $result['items'] = $view->result;
    
    if($view->usePager()){
      $result['pager'] = SELF::mountPager($view);
    }
    
    return $result;
  }
  
  /**
   * Monta paginação
   * 
   * @param object $view
   * @param array $options
   * @return boolean
   */
  private static function mountPager($view){
    $pager = array();
    $totalPages = ceil(($view->total_rows / $view->getItemsPerPage()));
    
    $pager['currentPage'] = $view->getCurrentPage();
    $pager['totalItems'] = $view->total_rows;
    $pager['itemsPerPage'] = $view->getItemsPerPage();
    $pager['totalPages'] = $totalPages;
    
    for ($i = 1; $i <= $totalPages; $i++) {
      $index = $i - 1;
      $label = $i;
      $link = 'page='.$index;
      
      $pager['pagination'][$index]['label'] = $label;
      $pager['pagination'][$index]['link'] = $link;
      if($view->getCurrentPage() == $index){
        $pager['pagination'][$index]['current'] = TRUE;
      }
    }
    
    return $pager;
  }
}
