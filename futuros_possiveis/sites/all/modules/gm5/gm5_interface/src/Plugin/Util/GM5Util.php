<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Util;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Database\Query\AlterableInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\Core\Path\AliasManager;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\views\Plugin\Derivative\ViewsLocalTask;
use Drupal\views\ViewExecutable;
use Drupal\views\Entity\View;
use Drupal\views\Render\ViewsRenderPipelineMarkup;
use Drupal\views\Views;
use Drupal\field\FieldConfigInterface;
use Drupal\node\Entity\Node;

class GM5Util
{

  //Redireciona para endereços internos e externos
  public static function redirect($path, $type = 'internal')
  {
    if ($type == 'internal') {
      $url = Url::fromUserInput($path, array('absolute' => TRUE))->toString();
    } else {
      $url = $path;
    }

    $redirect = new RedirectResponse($url, 301);
    $redirect->send();
    return;
  }

  public static function isMobile($useragent)
  {
    return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
  }

  //Renderiza url a partir de uri tipo entity:node/2 e https://google.com
  public static function uriToUrl($uri)
  {
    $itemObj = Url::fromUri($uri);

    $item = array();


    //Tipa e renderiza URL externa
    if ($itemObj->isExternal()) {
      $item['type'] = 'external';
      $item['target'] = '_blank';
      $item['url'] = $itemObj->getUri();
    }

    //Tipa e renderiza URL interna
    if ($itemObj->isRouted()) {
      $item['type'] = 'internal';
      $item['target'] = '_self';
      $item['url'] = $itemObj->toString();
    }

    return $item;
  }

  //SendEmail
  public static function sendMail($nameFrom, $emailFrom, $destinations, $subject, $message, $charset = 'iso-8859-1')
  {
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=' . $charset . "\r\n";
    $headers .= 'From: ' . $nameFrom . ' <' . $emailFrom . '>' . "\r\n" .
      'Reply-To: ' . $site_email . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
    $sendedMail = mail($destinations, $subject, $message, $headers);
    return $sendedMail;
  }

  //Renderiza alias a partir de path tipo /node/1
  public static function getAlias($path, $type = 'absolute')
  {
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath($path);
    if ($type == 'absolute') {
      $alias = \Drupal\Core\Url::fromUserInput($alias, array('absolute' => TRUE))->toString();
    }
    return $alias;
  }

  //Retorna o id de um vídeo do youtube com base em uma url
  public static function getYoutubeId($param)
  {
    if (strpos($param, 'https://') !== false || strpos($param, 'http://') !== false) {
      $param = str_replace('https://', '', $param);
      $param = str_replace('http://', '', $param);
    }
    $scheme = 'http://';
    $href = $scheme . str_replace('www.', '', $param);
    $href = str_replace('http://http://', 'http://', $href);

    $url = parse_url($href);

    if ($url['host'] == 'youtu.be') {
      return str_replace('/', '', $url['path']);
    } elseif (isset($url['query'])) {
      $query = array();
      parse_str($url['query'], $query);
      if (isset($query['v'])) {
        return $query['v'];
      }
    } else {
      if (strpos($url['path'], '/v/') !== false) {
        return str_replace('/v/', '', $url['path']);
      } else {
        return $url['path'];
      }
    }
  }

  public static function getYoutubeImage($vid)
  {
    return 'https://img.youtube.com/vi/' . $vid . '/hqdefault.jpg';
  }

  //Renderiza menu a partir d euma machine name
  public static function menuRender($machineName, $removeParentLink = FALSE)
  {
    //Inicializa menu
    $menu_name = $machineName;
    $menu_tree = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
    $tree = $menu_tree->load($menu_name, $parameters);


    $manipulators = array(
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
      array('callable' => 'toolbar_menu_navigation_links'),
    );
    $tree = $menu_tree->transform($tree, $manipulators);

    $menu = $menu_tree->build($tree);


    //Carrega itens de menu
    $menuItems = array();
    if (isset($menu['#items']) && !empty($menu['#items'])) {
      foreach ($menu['#items'] as $k => &$itemObj) {
        $item = array();

        //Label do mennu
        $item['title'] = $itemObj['title'];

        //Seta como ativo
        if ($itemObj['in_active_trail']) {
          $item['status'] = 'active';
        }

        //Tipa e renderiza URL externa
        if ($itemObj['url']->isExternal()) {
          $item['type'] = 'external';
          $item['url'] = $itemObj['url']->getUri();
        } else

          //Tipa e renderiza URL interna
          if ($itemObj['url']->isRouted()) {
            $item['type'] = 'internal';
            $item['url'] = $itemObj['url']->toString();
          } else {
            $item['type'] = 'other';
            $item['url'] = $itemObj['url']->toString();
          }

        $menuItems[$k] = $item;
      }
    }


    //Submenu
    $subtrees = array();
    foreach ($tree as $k => $element) {

      $parent = explode(' ', $k);
      $parent = end($parent);

      if ($element->subtree) {
        $subtree = $menu_tree->build($element->subtree);

        $subMenuItems = array();
        foreach ($subtree['#items'] as &$itemObj) {

          $item = array();

          //Label do mennu
          $item['title'] = $itemObj['title'];

          //print_r($itemObj); die;

          //Seta como ativo
          if ($itemObj['in_active_trail']) {
            $item['status'] = 'active';
          }

          //Tipa e renderiza URL externa
          if ($itemObj['url']->isExternal()) {
            $item['type'] = 'external';
            $item['url'] = $itemObj['url']->getUri();
          }

          //Tipa e renderiza URL interna
          if ($itemObj['url']->isRouted()) {
            $item['type'] = 'internal';
            $item['url'] = $itemObj['url']->toString();
          }
          $subMenuItems[] = $item;
        }

        if ($removeParentLink) {
          unset($menuItems[$parent]['url']);
        }
        $menuItems[$parent]['subMenu'] = $subMenuItems;
      }
    }
    return $menuItems;
  }

  //Convert array em objeto
  public static function arrayToObj($array)
  {
    return json_decode(json_encode($array));
  }

  //
  public static function contentTypeUrlFromNode($node)
  {
    $fullUrl = SELF::getAlias('/node/' . $node->nid->value);
    $partOfUrl = '/' . GM5String::transliteration($node->title->value);
    $url = str_replace($partOfUrl, '', $fullUrl);
    return $url;
  }

  //Valida latitude e longitude
  public static function validateGeoCoords($lat = null, $lng = null)
  {
    return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $lat . ',' . $lng);
  }

  public static function mesBr($mes){
    $meses = array(
      '01'=>'janeiro',
      '02'=>'fevereiro',
      '03'=>'março',
      '04'=>'abril',
      '05'=>'maio',
      '06'=>'junho',
      '07'=>'julho',
      '08'=>'agosto',
      '09'=>'setembro',
      '10'=>'outubro',
      '11'=>'novembro',
      '12'=>'dezembro',
    );

    return $meses[$mes];
  }

  public static function restFormat($page)
  {
    $restRequest = \Drupal::request()->query->get('restFormat');
    $pathFromRequest = \Drupal::request()->query->get('path');

    if (!empty($restRequest)) {
      $path = '$page["customPage"]';

      if (!empty($pathFromRequest)) {
        $path .= '["' . str_replace('.', '"]["', $pathFromRequest) . '"]';
      }

      eval('$src = ' . $path . ';');

      if ($restRequest == 'json') {
        header("Content-Type: application/json;charset=utf-8");
        if (!$src) {
          $json['status'] = false;
          $json['message'] = 'Estrutura de dados inválida ou indisponível.';
        } else {
          $json = array();
          $json['status'] = true;
          $json['message'] = null;
          $json['data'] = $src;
        }
        header('Content-Type: application/json');
        print json_encode($json);
        die;
      } else {
        header('Content-type: text/html; charset=UTF-8');
        if (!$src) {
          print 'Estrutura de dados inválida ou indisponível.';
          die;
        } else {
          print_r($src);
          die;
        }
      }
    }
  }

}
