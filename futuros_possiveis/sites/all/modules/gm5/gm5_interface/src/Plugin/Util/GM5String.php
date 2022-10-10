<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace Drupal\gm5_interface\Plugin\Util;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Transliteration\PhpTransliteration;

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
use Drupal\gm5_interface\Plugin\Util\GM5String;



class GM5String {
  //Método que fecha tags
  public static function closeTags($html) {
    preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
      return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i = 0; $i < $len_opened; $i++) {
      if (!in_array($openedtags[$i], $closedtags)) {
        $html .= '</' . $openedtags[$i] . '>';
      } else {
        unset($closedtags[array_search($openedtags[$i], $closedtags)]);
      }
    }
    return $html;
  }

  //Método limitador de texto
  public static function textLimit($texto, $limite, $method = "nospace") {
    $fttxt = $texto;
    $qtn = strlen(strip_tags($fttxt));

    if ($method == "simple") {
      if ($qtn > $limite) {
        $texto = trim(
                substr(
                    $fttxt, 0, $limite
                )
            ) . '...';
      }
    } else {
      if ($qtn >= $limite) {
        $texto = substr(
                $fttxt, 0, strrpos(
                    substr(
                        $fttxt, 0, $limite
                    ), ' '
                )
            ) . '...';
      }
    }

    return SELF::closeTags($texto);
  }
  
  public static function formataReal($num){
      return 'R$' . number_format($num, 2, ',', '.'); // retorna R$100.000,50
  }
  
  public static function transliteration($string,$lang = 'pt-br'){
    $trans = new PHPTransliteration();
    $transformed = $trans->transliterate($string, $lang);
    $clean_url =  preg_replace('/\-+/', '-', strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '', str_replace(' ', '-', $transformed))));
    return $clean_url;
  }
  
  //Método que limpa string Captalizando a primeira letra e deixando as demais em caixa baixa
  public static function textCase($string, $method = 'onlyfirst') {
    $str = trim($string);
    if ($method == 'onlyfirst') {
      $fstr = mb_convert_case(mb_substr($str, 0, 1), MB_CASE_UPPER, 'UTF-8');
      $mstr = mb_convert_case(mb_substr($str, 1), MB_CASE_LOWER, 'UTF-8');
      return $fstr . $mstr;
    } else if ($method == 'allupper') {
      return mb_convert_case($str, MB_CASE_UPPER, 'UTF-8');
    } else if ($method == 'alllower') {
      return mb_convert_case($str, MB_CASE_LOWER, 'UTF-8');
    } else if ($method == 'allcap') {
      return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    }
  }
  
  //os Valores para $part devem ser %B %b %a %A (%B = Janeiro / %b = Jan / %a = Qui / %A = Quinta);
  public static function dateBr($date, $part, $format = 'd/m/Y') {
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');

    $cvdate = date_parse_from_format($format, $date);
    $timestamp = mktime(0, 0, 0, $cvdate['month'], $cvdate['day'], $cvdate['year']);
    return SELF::textCase(gmstrftime($part, $timestamp));
  }
  
  public static function clearTime($time){
    if (strpos($time, ':') !== false && strlen($time) > 5) {
		$rest = substr($time, 0, -3);  // returns "cde"
		return $rest;
	} else {
		return $time;
	}
  }
}

