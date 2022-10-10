<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Shopify;

use GuzzleHttp\Client;

class GM5Shopify
{
    public static function getCursos()
    {
        //$url = "https://f7d51a0b1c51b01a6955a7f4f5f52f47:9a62548dccd3d6b83994b63879a0792f@loja-will.myshopify.com/admin/api/2019-10/products.json";
        $url = "https://8a5400189e2a0c0ea47ee45b7bd65486:0dced1dc2e89c467b2facc515bd9cccb@cursos-senai-ead.myshopify.com/admin/api/2019-07/products.json?limit=250";
          
        $client = new Client();

        $resp = $client->get($url);
        $respdata = $resp->getBody()->getContents();

        $data = json_decode($respdata);
        
        $obj = array();
        
        foreach ($data->products as $key => $value) {
            $obj[$key]['product_id'] = $value->options[0]->product_id;
            $obj[$key]['title'] = $value->title;
            $obj[$key]['update'] = $value->updated_at;
            $obj[$key]['price'] = $value->variants[0]->price;
            $obj[$key]['body'] = $value->body_html;
            
            $ex = explode('|', $value->variants[0]->barcode);
            foreach($ex as $value){
                $temp = explode('=', $value);
                $obj[$key][$temp[0]] = $temp[1];
            }
            
            // $obj[$key]['tags'] = $value->tags;
            // $obj[$key]['image'] = $value->image->src;
            // $obj[$key]['published'] = $value->published_at;
            // $obj[$key]['created'] = $value->created_at;
        }
        
        return $obj;
        
    }
}