<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Util;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class GM5Validation {

  //Mensagens de erro
  public static $formErrorMessage = array(
      'fileRequired' => 'O campo #label# é obrigatório.',
      'required' => 'O campo #label# é obrigatório.',
      'email' => 'O campo #label# deve ser preenchdio corretamente.',
      'fileType'=>'O Arquivo enviado no campo #label# deve ser de um dos tipos permitidos: #tipos#.',
      'fileSize'=>'O Arquivo enviado no campo #label# deve ter no máximo #tamanho#.'
  );

  //Executa validações a partir dos fields do form
  public function autoValid(&$fields) {
    foreach ($fields as $name => $obj) {

      //Atribui o valor preenchido ao campo
      if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $fields[$name]['value'] = is_array($_POST[$name]) ? implode(', ',$_POST[$name]) : $_POST[$name];
      }

      //Passa pelas validações requeridas para o campo
      if (isset($fields[$name]['valid'])) {
        foreach ($fields[$name]['valid'] as $valid) {
          $callBack = forward_static_call(array('SELF', $valid), $obj, $valid);
          if (isset($callBack['error'])) {
            $fields[$name] = $callBack;
            break;
          }
        }
      }
    }

    return $this->getFormStatus($fields);
  }

  //Resume mensagens de erro em uma lista
  public function getFormStatus($fields) {
    $erros = array();
    foreach ($fields as $obj) {
      if (isset($obj['error'])) {
        $erros['type'] = 'error';
        $erros['messages'][$obj['name']] = $obj['error'];
      }
    }
    return $erros;
  }

  //Valida campo obrigatório
  public static function required($field, $valid) {
    if (!isset($_POST[$field['name']]) or empty($_POST[$field['name']])) {
      $field['error'] = str_replace('#label#', $field['label'], SELF::$formErrorMessage[$valid]);
    }
    return $field;
  }

  public static function fileRequired($field, $valid) {
    if (!isset($_FILES[$field['name']]) or empty($_FILES[$field['name']]) or $_FILES[$field['name']]['error'] === 4) {
      $field['error'] = str_replace('#label#', $field['label'], SELF::$formErrorMessage[$valid]);
    }
    return $field;
  }

  public static function fileType($field, $valid) {
    
    if(isset($_FILES[$field['name']]) && $_FILES[$field['name']]['error'] === 0){
      $allowed = array('pdf', 'doc', 'docx');
      $filename = $_FILES[$field['name']]['name'];
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      if (!in_array($ext, $allowed)) {
        $field['error'] = str_replace('#tipos#',implode(', ',$allowed),str_replace('#label#', $field['label'], SELF::$formErrorMessage[$valid]));
      }
    } else {
      $field['error'] = 'Arquivo não informado';
    }
    return $field;
  }
  
  public static function fileSize($field, $valid) {
    if(isset($_FILES[$field['name']]) && $_FILES[$field['name']]['error'] === 0){
      $max = 8;
      $maxSize = $max * 1024 * 1024; //10MB
      $fileSize = filesize($_FILES[$field['name']]['tmp_name']);
      
      if($fileSize > $maxSize){
        $field['error'] = str_replace('#tamanho#',$max.'MB',str_replace('#label#', $field['label'], SELF::$formErrorMessage[$valid]));
      }
    } else {
      $field['error'] = 'Arquivo não informado';
    }
    return $field;
  }

  //Valida email
  public static function email($field, $valid) {
    $conta = "^[a-zA-Z0-9\._-]+@";
    $domino = "[a-zA-Z0-9\._-]+.";
    $extensao = "([a-zA-Z]{2,4})$";
    $pattern = $conta . $domino . $extensao;
    if (!ereg($pattern, $_POST[$field['name']])) {
      $field['error'] = str_replace('#label#', $field['label'], SELF::$formErrorMessage[$valid]);
    }
    return $field;
  }

}
