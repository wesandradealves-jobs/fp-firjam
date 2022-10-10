<?php

/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloWorldController.
 */

namespace Drupal\gm5_autocomplete\Plugin\Autocomplete;

use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Drupal\Component\Transliteration\PhpTransliteration;
use Drupal\node\Entity\Node;


class Autocomplete
{
    public $startUrl;
    public $locais;

    public function __construct()
    {
        $solr_endpoint = Settings::get('om_solr_endpoint');
        $this->startUrl = "$solr_endpoint/solr/";
    }

    public function checkExists($titulo)
    {
        $url = $this->startUrl . 'bbrk_autocomplete1/select?fq=titulo:"' . $titulo . '"&indent=on&q=*:*&wt=json';
        $data = $this->getHttpData($url);

        if (count($data->response->docs) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getLocationItem($titulo)
    {
        $url = $this->startUrl . 'bbrk_autocomplete1/select?fq=titulo:"' . $titulo . '"&indent=on&q=*:*&wt=json';

        $data = $this->getHttpData($url);


        if (count($data->response->docs) > 0) {
            return $data->response->docs;
        } else {
            return false;
        }
    }

    public function index()
    {
        $this->getLocations();
        print 'tudo indexado';
    }

    public function clear()
    {
        $this->clearSolr();
    }

    public function response($term,$coords = '',$subsidiaria='')
    {
        if (isset($term)) {
//            $container = \Drupal::getContainer();
//            $temp_store_factory = $container->get('user.private_tempstore');
//            $tempStore = $temp_store_factory->get('om_localizacao');
            $subsidiariaCompleta = '';
            if(!empty($subsidiaria)){
                $subsidiariaCompleta = '&fq=unidade_negocio:("'.$subsidiaria.'")';
            }

            $coordsComplete = '';
            if(!empty($coords)){
                $coordsComplete = '&fq={!geofilt}&sfield=geoleocation&pt='.$coords.'&d=100000&sort=geodist()%20asc';
            }
            //print $this->startUrl; die;

            return file_get_contents('http://'.$this->startUrl.'bbrk_autocomplete1/select?q=search_ngram:"' . urlencode($term) . '"'.$coordsComplete.$subsidiariaCompleta.'&wt=json');
        }

    }

    public function addSolrItem($data)
    {
        $add = array(
            "add" => array(
                "doc" => array(),
            ),
            "commit" => (object)[]
        );

        $add['add']['doc'] = $data;

        $url = $this->startUrl . 'bbrk_autocomplete1/update?wt=json';
        $data = $this->postHttpData($url, $add);

    }

    public function clearSolr()
    {

        $delete = array(
            "delete" => array(
                "query" => "*:*",
            ),
            "commit" => (object)[]
        );


        $url = $this->startUrl . 'bbrk_autocomplete1/update?wt=json';

        $data = $this->postHttpData($url, $delete);
        print 'solr limpo';
    }

    public function getHttpData($bUrl, $data = array())
    {
        $client = new Client();
        $url = preg_replace('/\s+/S', " ", $bUrl);

        $resp = $client->get($url);
        $respdata = $resp->getBody()->getContents();

        $data = json_decode($respdata);

        return $data;
    }

    public function postHttpData($bUrl, $data = array())
    {
        $client = new Client();
        $url = preg_replace('/\s+/S', " ", $bUrl);

        $request = $client->post($url, [
            RequestOptions::JSON => $data
        ]);
        //print_r($data); die;

        $respdata = $request->getBody()->getContents();

        $data = json_decode($respdata);

        return $data;
    }

    public function getLocations()
    {
        $url = $this->startUrl . 'bbrk_site1/select?fl=its_uf,its_cidade,ss_uf,ss_cidade,ss_url,ss_composicao_url,ss_bairro,terms_ts_field_referencia,ss_unidade_negocio,ss_title,ss_type&indent=on&q=*:*&wt=json&rows=1000000';

        $data = $this->getHttpData($url);

        foreach ($data->response->docs as $value) {
            $localTmp = array();
            $localTmpCidade = array();
            if (isset($value->ss_cidade) && !empty($value->ss_cidade)) {
                if (isset($value->ss_bairro) && !empty($value->ss_bairro)) {
                    $titulo = $value->ss_bairro . ', ' . $value->ss_cidade . ' - ' . $value->ss_uf;
                    $localTmp["search"] = $this->tirarAcentos($value->ss_bairro);
                    $localTmp["bairro"] = $value->ss_bairro;
                    $localTmp["tipo"] = 'bairro';
                    if(isset($value->ss_unidade_negocio) && !empty($value->ss_unidade_negocio)){
                        $localTmp["unidade_negocio"][$value->ss_unidade_negocio] = $value->ss_unidade_negocio;
                    }
                    
                    $localTmp["id"] = $this->transliteration($titulo);
                    $localTmp["cidade"] = $value->ss_cidade;
                    $localTmp["uf"] = $value->ss_uf;
                    $localTmp["id_cidade"] = $value->its_cidade;
                    $localTmp["id_uf"] = $value->its_uf;
                    $localTmp["titulo"] = $titulo;
                    if(!isset($this->locais[$this->transliteration($titulo)])){
                        $this->locais[$this->transliteration($titulo)] = $localTmp;
                    } else {
                        if(isset($value->ss_unidade_negocio) && !empty($value->ss_unidade_negocio)){
                            $this->locais[$this->transliteration($titulo)]["unidade_negocio"][$value->ss_unidade_negocio] = $value->ss_unidade_negocio;
                        }
                    }
                }


                $tituloCidade = $value->ss_cidade . ' - ' . $value->ss_uf;
                if(!isset($this->locais[$this->transliteration($tituloCidade)])){

                    $query = \Drupal::entityQuery('node');
                    $query->condition('status', 1);
                    $query->condition('type', 'cidade');
                    $query->condition('title', $value->ss_cidade);
                    $query->condition('field_estado', $value->its_uf);
                    $entity_ids = $query->execute();

//                    $unidadN = '';
//                    if(!empty($entity_ids)){
//                        $objCidade = Node::load(end($entity_ids));
//                        //$unidadN = $objCidade->field_regional->entity->field_unidade_de_negocio->entity->getTitle();
//                        if(isset($objCidade->field_regional->entity) && isset($objCidade->field_regional->entity->field_unidade_de_negocio->entity)){
//                            $unidadN = $objCidade->field_regional->entity->field_unidade_de_negocio->entity->getTitle();
//                        } else {
//                            //print $value->ss_cidade.' - '.$value->ss_uf.'<br>';
//                        }
//                    }


                    $localTmpCidade["search"] = $this->tirarAcentos($value->ss_cidade);
                    $localTmpCidade["tipo"] = 'cidade';
                    if(isset($value->ss_unidade_negocio) && !empty($value->ss_unidade_negocio)){
                        $localTmpCidade["unidade_negocio"][$value->ss_unidade_negocio] = $value->ss_unidade_negocio;
                    }
                    //$localTmpCidade["unidade_negocio"][$value->ss_unidade_negocio] = $value->ss_unidade_negocio;
                    //$localTmpCidade["unidade_negocio"] = $value->ss_unidade_negocio;
                    $localTmpCidade["id"] = $this->transliteration($tituloCidade);
                    $localTmpCidade["cidade"] = $value->ss_cidade;
                    $localTmpCidade["uf"] = $value->ss_uf;
                    $localTmpCidade["id_cidade"] = $value->its_cidade;
                    $localTmpCidade["id_uf"] = $value->its_uf;
                    $localTmpCidade["titulo"] = $tituloCidade;

                    $this->locais[$this->transliteration($tituloCidade)] = $localTmpCidade;
                } else {
                    if(isset($value->ss_unidade_negocio) && !empty($value->ss_unidade_negocio)){
                        $this->locais[$this->transliteration($tituloCidade)]["unidade_negocio"][$value->ss_unidade_negocio] = $value->ss_unidade_negocio;
                    }
                }
            }

        }

        foreach($this->locais as $key => $local){
            $this->locais[$key]['unidade_negocio'] = array_values($local['unidade_negocio']);
        }

        foreach ($this->locais as $key => $local) {
            if ($this->checkExists($local['titulo'])) {
                unset($this->locais[$key]);
            } else {
                //$this->addSolrItem($local);
                $local["geoleocation"] = implode(',', $this->getLatLong($local['titulo']));
                if (!empty($local["geoleocation"])) {
                    $this->addSolrItem($local);
                }

            }

        }


    }

    public function getLatLong($dlocation)
    {
        $address = $dlocation; // Google HQ
        $prepAddr = str_replace(' ', '+', $address);
        //AIzaSyAbRNmLHKVvhTQ1c_2eypu3L7ZCnp5gvUg
        $output = $this->getHttpData('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false&key=AIzaSyAbRNmLHKVvhTQ1c_2eypu3L7ZCnp5gvUg');

        if (!$output || count($output->results) == 0) {
            $myfile = file_put_contents('logs_autocomplete.txt', $address . ' | ' . $output->error_message . PHP_EOL, FILE_APPEND | LOCK_EX);
            return [];
        }
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        return [$latitude, $longitude];
    }


    public function transliteration($string, $lang = 'pt-br')
    {
        $trans = new PHPTransliteration();
        $transformed = $trans->transliterate($string, $lang);
        $clean_url = preg_replace('/\-+/', '-', strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '', str_replace(' ', '-', $transformed))));
        return $clean_url;
    }

    private function tirarAcentos($string)
    {
        return strtolower(preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string));
    }
}