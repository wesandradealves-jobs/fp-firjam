<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloWorldController.
 */

namespace Drupal\gm5_interface\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\gm5_interface\Plugin\Simulador\Calculadora;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use \Drupal\gm5_interface\Plugin\Node\GM5Webform;
use Drupal\gm5_interface\Plugin\Simulador\SendToCRM;

//
//	Função que valida idade
//
function calcula_idade($data)
{
  $data_nasc = explode("/", $data);
  $dataHj = date("d-m-Y");
  $dataHj = explode("-", $dataHj);
  // Solução de contorno criativo para resolver o problema do bug de 2038
  if ($data_nasc[2] <= 1901 || $data_nasc[2] >= 2038) {
    return ($dataHj[2] - $data_nasc[2]);
  } else {
    list($dia, $mes, $ano) = explode('/', $data);
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $nascimento = mktime(0, 0, 0, $mes, $dia, $ano);
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
  }
}

//
//	Função que valida cpf
//
function validar_cpf($cpf)
{

  $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);

  if (strlen($cpf) != 11)
    return false;

  for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--)
    $soma += $cpf[$i] * $j;

  $resto = $soma % 11;

  if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto))
    return false;

  for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--)
    $soma += $cpf[$i] * $j;

  $resto = $soma % 11;

  return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);

}

//
//	Valida data
//
function ValidaData($dat)
{
  $data = explode("/", "$dat"); // fatia a string $dat em pedados, usando / como referência
  $d = $data[0];
  $m = $data[1];
  $y = $data[2];

  $res = checkdate($m, $d, $y);
  if ($res == 1) {
    return true;
  } else {
    return false;
  }
}

//
//	Moeda
//
function moeda($get_valor)
{
  $source = array('.', ',');
  $replace = array('', '.');
  $valor = str_replace($source, $replace, $get_valor);
  return $valor;
}

class GM5WebformProcess extends ControllerBase
{

  public function content()
  {
    $params = \Drupal::request()->request->all();
    //print_r($params); die;
    $response = GM5Webform::processForm($params);
    return new JsonResponse($response);
  }

  
}
