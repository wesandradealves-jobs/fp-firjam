<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Util;

use Drupal\Core\Site\Settings;
use Memcached;

/**
 * Description of GM5Memcached
 *
 * @author pferraz
 */
class GM5Memcached {
    public $memObj;
    
    public function __construct() {
        if (class_exists('Memcached')) {
            $memcachedEndPoint = explode(':', array_keys(Settings::get('memcache')['servers'])[0])[0];
            $this->memObj = new Memcached;
            $this->memObj->addServer($memcachedEndPoint, 11211);
        } else {
            print 'Memcached não instalado'; die;
        }
    }

    public function getCache($key){
        if ($menOferta = $this->memObj->get($key)) {
            return $menOferta;
        } else {
            return false;
        }
    }
    
    public function setCache($key, $obj){
        $this->memObj->set($key, $obj);
    }
}
