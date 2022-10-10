<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace Drupal\gm5_interface\Plugin\File;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

class GM5File extends File
{
    //Carrega a partir de um fid
    public static function renderImage($fid, $style = NULL)
    {
        $file = File::load($fid);
        if ($file) {
            $path = $file->getFileUri();
            if ($style) {
                $imgObj = ImageStyle::load($style);
                $url = '';
                if ($imgObj) {
                    $url = $imgObj->buildUrl($path);
                }
            } else {
                $url = file_create_url($path);
            }
            return $url;
        } else {
            return false;
        }

    }

    //Carrega a partir de um array de fids
    public static function renderMultipleImage($fids, $style = NULL)
    {
        $files = entity_load_multiple('file', $fids);
        $images = array();
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = $file->getFileUri();
                if ($style) {
                    $images[$file->id()] = ImageStyle::load($style)->buildUrl($path);
                } else {
                    $images[$file->id()] = file_create_url($path);
                }
            }
        }

        return $images;
    }
}