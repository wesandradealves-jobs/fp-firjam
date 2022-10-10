<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Node;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class GM5Node extends Node {

    //Carrega um node a partir de um alias 
    public static function loadByAlias($alias) {
        $system_path = \Drupal::service('path_alias.manager')->getPathByAlias($alias);
        if (strpos($system_path, '/node/') !== false) {
            $nid = str_replace('/node/', '', $system_path);
            return Node::load($nid);
        }
    }

    //Retorna alias a partir de um nid
    public static function getAlisbyNid($nid) {
        $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
        return $alias;
    }

    //Carrega uma taxonomia a partir do id
    public static function taxonomyTermLoad($tid) {
        return Term::load($tid);
    }

    //Carrega uma taxonomia a partir de um name
    public static function taxonomyTermLoadByName($name) {
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $name]);
        return $term;
    }

    //Carrega uma árvore de taxonomia setando um nível máximo
    public static function taxonomyLoadTree($vocabulary, $maxTreeLevel = 1, $loadEntity = FALSE) {
        $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary, 0, $maxTreeLevel, $loadEntity);
        return $tree;
    }

    /**
     * Retorna os nids de um tipo de conteúdo que estejam publicados
     * @param string $machineName
     * @return array
     */
    public static function loadNidsByContenttype($machineName) {
        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $query->condition('type', $machineName);
        $entity_ids = $query->execute();
        
        return $entity_ids;
    }

    /**
     * Retorna primeiro nível de filhos de um pai
     * @param $tid
     * @param null $voc
     * @return array
     */
    public static function taxonomyChildrensFromParent($tid, $voc = NULL) {
        $terms = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_term')
                ->loadChildren($tid, $voc);
        $results = [];
        foreach ($terms as $id => $term) {
            $results[$id] = $term->label();
        }
        return $results;
    }
    
    public static function taxonomyGetParent($tid, $voc = NULL) {
        $terms = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_term')
                ->loadParents($tid, $voc);
        $results = [];
        foreach ($terms as $id => $term) {
            $results[$id] = $term->label();
        }
        return $results;
    }

}
