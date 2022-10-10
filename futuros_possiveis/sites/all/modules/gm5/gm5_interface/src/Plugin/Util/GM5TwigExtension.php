<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Util;

use \Drupal\Core\Template\Twig_Extension;

class GM5TwigExtension extends \Twig_Extension {
    
    //
    // Declarando o nome da extenção do twig
    //
    public function getName() {
        return  'gm5_interface.GM5TwigExtension' ;
    }
    
    //
    // Retornando as funções customizadas
    //
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('fileVersion', array($this, 'fileVersion'))
        );
    }
    
    //
    //Funçõeos customizadas
    //
    
    // Aplica parametro com data de modificação do arquivo
    // A roda deve ser a partir da raiz do drupal
    public function fileVersion($css) {
        $css = substr($css, 0, 1) == '/' ? substr($css, 1) : $css;
        return '/'.$css.'?v='.filemtime($css);
    }
  
}