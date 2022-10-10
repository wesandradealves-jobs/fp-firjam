<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\gm5_interface\Plugin\Node;

use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\Entity\Webform;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Url;
use Drupal\gm5_interface\Plugin\Util\GM5Validation;

class GM5Webform
{

  private $formName;

  public function __construct($webformName)
  {

//        print_r(RequestStack::getCurrentRequest()); die;
//        $request = \Drupal::requestStack()->getCurrentRequest()->query->all();
//        if($request){
//            
//        }
//        print_r($request); die;
    //print_r(get_class_methods(\Drupal::request())); die;
    //print_r(\Drupal::request()->getMethod()); die;

    $this->formName = $webformName;
    //$data = \Drupal::request()->query->all();
    //if(!empty($data)){
    //print_r($data);die;
    //}
  }

  public static function getStucture($formName)
  {
    $webFormLoad = Webform::load($formName);
    $webFormObj = $webFormLoad->getSubmissionForm();
    //kint($webFormObj); die;
    $form = array();

    $form['action'] = Url::fromRoute('gm5_interface.webform')->toString();
    $params = \Drupal::request()->request->all();

    //print_r($params); die;

    foreach ($webFormObj['elements'] as $field) {
      //kint($field['radios']);

      $fieldTmp = array();
      if (isset($field['#name'])) {
        $fieldTmp['name'] = $field['#name'];
        $fieldTmp['type'] = $field['#type'];
        $fieldTmp['label'] = $field['#title'];
        $fieldTmp['required'] = $field['#required'];
        $fieldTmp['forId'] = $field['#webform_id'];
        $fieldTmp['value'] = isset($params[$field['#name']]) ? $params[$field['#name']] : '';

        if (isset($field['#options'])) {
          unset($field['#options']['']);
          $fieldTmp['options'] = array();
          foreach ($field['#options'] as $value => $option) {
            $optionTmp = array();
            $optionTmp['value'] = $value;
            $optionTmp['label'] = $option;
            $fieldTmp['options'][] = $optionTmp;
          }
        }

        $form['fields'][$field['#name']] = $fieldTmp;
      }
    }

    $form['hiddens']['form_name'] = $formName;
    //$form['hiddens']['form_build_id'] = $webFormObj['form_build_id']['#value'];
    //$form['hiddens']['form_id'] = $webFormObj['form_id']['#value'];

    if (isset($webFormObj['form_token']) && !empty($webFormObj['form_token'])) {
      $form['hiddens']['form_token'] = $webFormObj['form_token']['#value'];
    }
    return $form;
  }

  public static function processForm($data)
  {
    // Get submission values and data.
    $values = [
      'entity_type' => NULL,
      'entity_id' => NULL,
      'in_draft' => FALSE,
      'uid' => \Drupal::currentUser()->id(),
      //'langcode' => 'en',
      'token' => isset($data['form_token']) ? $data['form_token'] : 'pgmJREX2l4geg2RGFp0p78Qdfm1ksLxe6IlZ-mN9GZI',
      'uri' => '/process/webform',
      'remote_addr' => \Drupal::request()->getClientIp(),
      //'data' => [
      //    'checkboxes_field' => [
      //      'Option 1',
      //      'Option 2',
      //      'Option 3',
      //    ],
      //'nome' => 'teste',
      //'radio_field' => 'Mail',
      //'e_mail' => 'myemailcom',
      //],
    ];



    $values['webform_id'] = $data['form_name'];
    unset($data['form_name']);
    if (isset($data['form_token'])) {
      $values['token'] = $data['form_token'];
      unset($data['form_token']);
    } else {
      $values['token'] = 'pgmJREX2l4geg2RGFp0p78Qdfm1ksLxe6IlZ-mN9GZI';
    }
    unset($data['op']);
    $values['data'] = $data;

    $webform = Webform::load($values['webform_id']);
    $is_open = WebformSubmissionForm::isOpen($webform);

//        // Validação reCaptcha
//        $secret="6LcwXmYUAAAAAPiSfZSvTpD_pYTnNkLJARdlQVz1";
//        if($values['webform_id'] == 'simulador' && isset($data["recaptcha"])){
//            $resp = $data["recaptcha"];
//        }else if ($values['webform_id'] != 'simulador' && isset($data["g-recaptcha-response"])){
//            $resp = $data["g-recaptcha-response"];
//        } else {
//            $resp = '';
//        }
//        $verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$resp}");
//        $captcha_success=json_decode($verify);



    if ($is_open === TRUE) {
      $response = array();
      $errors = WebformSubmissionForm::validateFormValues($values);
      $message = array();
      if (!empty($errors)) {
        $response['type'] = 'error';
        foreach ($errors as $key => $errorObject) {
          $message[$key] = $errorObject->render();
        }
      }
//            else if($values['webform_id'] != 'newsletter' && empty($captcha_success->success))
//            {
//                $response['type'] = 'error';
//                $message['captcha'] = 'Preencha o Captcha';
//                ob_start();
//                print "erro captcha ";
//                \Drupal::logger('gm5_interface')->debug(ob_get_clean());
//            }
//            else if($values['webform_id'] == 'parceiros' && !GM5Validation::cnpj($data['cnpj']))
//            {
//                $response['type'] = 'error';
//                $message['cnpj'] = 'Digite um CNPJ válido';
//                ob_start();
//                print "erro cnpj ";
//                \Drupal::logger('gm5_interface')->debug(ob_get_clean());
//            }
      else {
        $response['type'] = 'sucess';
        $message['confirmMessage'] = 'Dados enviados com sucesso.';

        $all_files = \Drupal::request()->files->get('files', []);

        foreach($all_files as $field => $dataFile){
          if(isset($all_files[$field]) && !empty($all_files[$field])){
            $file = $all_files[$field];
            $filename = $file->getClientOriginalName();
            $file_path = $file->getRealPath();
            $file_save_path_stream_directory =  'private://ta-em-casa';
            $fileLocation = $file_save_path_stream_directory . '/' . $filename;
            \Drupal::service('file_system')->prepareDirectory($file_save_path_stream_directory, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY | \Drupal\Core\File\FileSystemInterface::MODIFY_PERMISSIONS);
            $file = file_save_data(file_get_contents($file_path), $fileLocation, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);

            $values['data'][$field] = $file->id();
          }
        }

        $webform_submission = WebformSubmissionForm::submitFormValues($values);



        if (isset($values['data']['redirect']) && !empty($values['data']['redirect'])) {
          $response = new RedirectResponse($values['data']['redirect']);
          $response->send();
          die;
        }
      }


      $response['message'] = $message;
      return $response;
    } else {
      return false;
    }
  }

}
