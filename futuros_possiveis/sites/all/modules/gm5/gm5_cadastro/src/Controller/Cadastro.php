<?php

/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloWorldController.
 */

namespace Drupal\gm5_cadastro\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\gm5_interface\Plugin\Util\GM5String;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\gm5_interface\Plugin\File\GM5File;
use Drupal\gm5_interface\Plugin\Util\GM5Util;
use Drupal\gm5_interface\Plugin\Views\GM5Views;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\file\Entity\File;
use Drupal\Core\StreamWrapper\PublicStream;

class Cadastro extends ControllerBase
{
    public function index(){

        $param = \Drupal::request()->request->all();

        if($param){
            /*
            $mgs = ['sucess' => 'Dados salvos com sucesso.'];
            $node = Node::create([
              'type'        => 'pre_cadastro',
              'title'       => $param['NomeCompleto'] . ' - ' . date('d/m/Y H:i'),
              'field_nome' => ['value' => $param['NomeCompleto']],
              'field_email' => ['value' => $param['Email']],
              'field_cpf' => ['value' => $param['CPF']],
              'field_nome_da_empresa' => ['value' => $param['NomeEmpresa']],
              'field_cnpj' => ['value' => $param['CNPJ']],
              'field_form' => ['value' => json_encode($param)],
              'field_aceita_os_termos' => ['value' => 1]
            ]);
            $node->save();
            */


            $idsEmail = \Drupal::entityQuery('user')
                        ->condition('mail', $param['Email'])
                        ->range(0, 1)
                        ->execute();
            $idsUser = \Drupal::entityQuery('user')
                        ->condition('field_cpf', $param['CPF'])
                        ->range(0, 1)
                        ->execute();

            $idsNacionalidade = \Drupal::entityQuery('user')
                        ->condition('field_passaporte', $param['Passaporte'])
                        ->range(0, 1)
                        ->execute();

            if(!empty($idsEmail)){
                $mgs = ['error' => 'Email já cadastrado'];
            } else if($param['Nacionalidade'] == 'brasileiro' && !empty($idsUser)){
                $mgs = ['error' => 'CPF já cadastrado'];
            } else if($param['Nacionalidade'] == 'estrangeiro' && !empty($idsNacionalidade)){
                $mgs = ['error' => 'Passaporte já cadastrado'];
            } else {
                $user = User::create(array(
                    'field_nome' => ['value' => $param['NomeCompleto']],
                    'name' => $param['Email'],
                    'mail' => $param['Email'],
                    'pass' => $param['Password'],
                    'field_nacionalidade' => ['value' => $param['Nacionalidade']],
                    'field_passaporte' => ['value' => $param['Passaporte']],
                    'field_cpf' => ['value' => $param['CPF']],
                    'field_nome_da_empresa' => ['value' => $param['NomeEmpresa']],
                    'field_cargo' => ['value' => $param['Cargo']],
                    'field_cnpj' => ['value' => $param['CNPJ']],
                    'field_como_ficou_sabendo' => ['value' => $param['FicouSabendo']],
                    'field_outra_forma_que_ficou_sabe' => ['value' => $param['OutroFicouSabendo']],
                    'field_notificado' => ['value' => 'sim'],
                    'field_aceita_os_termos' => ['value' => 1],
                    'status' => 1,
                ));
                $user->save();

                if(isset($param['Oficinas']) && !empty($param['Oficinas'])){
                    $userArray = $user->toArray();
                    $node = Node::create([
                      'type'        => 'incrito_oficina',
                      'title'       => $param['NomeCompleto'] . ' - ' . date('d/m/Y H:i'),
                      'field_inscrito' => ['target_id' => $userArray['uid'][0]['value']],
                      'field_nascimento' => ['value' => $param['Nascimento']],
                      'field_telefone' => ['value' => $param['Telefone']],
                      'field_cep' => ['value' => $param['CEP']],
                      'field_endereco' => ['value' => $param['Endereco']],
                      'field_numero' => ['value' => $param['Numero']],
                      'field_complemento' => ['value' => $param['Complemento']],
                      'field_oficinas' => explode('|',$param['Oficinas']),
                    ]);
                    $node->save();

                }

                // $op = 'register_no_approval_required';
                // _user_mail_notify($op, $user);

                $mgs = ['sucess' => 'Dados salvos com sucesso.'];

                // Envio do email de boas vindas
                $NomeCompleto = explode(' ',$param['NomeCompleto']);
                $firstName = $NomeCompleto[0];
                $to = $param['Email'];
                $email = $param['Email'];
                $senha = $param['Password'];
                $subject = 'Festival Futuros Possíveis 2022 | Confirmação de Inscrição';

                $module_handler = \Drupal::service('module_handler');
                $module_path = $module_handler->getModule('gm5_cadastro')->getPath();
                $body = "
                <!DOCTYPE html>
<html lang='en' xmlns='https://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
<head>
		<meta charset='utf-8'> <!-- utf-8 works for most cases -->
		<meta name='referrer' content='never'>
		<meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
		<meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
		<meta name='x-apple-disable-message-reformatting'>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
		<title>Firjan IEL</title> <!-- The title tag shows in email notifications, like Android 4.4. -->

		<style type='text/css'>
			body {
				background-color: #ebeaea;
			}
			table {
				border-collapse: collapse;
				border: 0;
			}
			table.container {
				width: 580px;
				background-color: #ffffff;
			}
			table.content {
				width: 520px;
			}
			/* Título */
			h1 {
				font-size: 1.6rem;
				color: #273b59; /*Cor do título */
				font-weight: normal;
				font-family: Trebuchet MS, Verdana, 'sans-serif';
				padding: 0;
				margin: 0;
			}
			
			/* Data e hora */
			h2 {
				font-size: 1.4rem;
				color: #273b59; /*Cor do destaque da data */
				font-family: Trebuchet MS, Verdana, 'sans-serif';
				padding: 0;
				margin: 0;
			}

			h4 {
				font-size: 1rem;
				color: #393230;  /* Cor do texto secundário */
				font-family: Trebuchet MS, Verdana, 'sans-serif';
				padding: 0;
				margin: 0;
				font-weight: normal;
			}

			h5 {
				font-size: .9rem;
				color: #9c9897; /* Cor do texto da tag */
				font-family: Trebuchet MS, Verdana, 'sans-serif';
				padding: 0;
				margin: 0;
				font-weight: normal;
			}
			
			/* Texto padrão */
			p {
				font-size: 1rem;
				color: #393230; /* Cor do texto padrão */
				font-family: Trebuchet MS, Verdana, 'sans-serif';
			}

			/* Link normal de texto */
			a {
				color: #273b59; /* Cor do link de texto */
			}


			/* Botão principal */
			a.btn {
				text-decoration: none;
				display: inline-block;
				font-family: Trebuchet MS, Verdana, 'sans-serif';
				text-decoration: none;
				color: #fefefe !important; /* Cor do texto do botão */
			}

			table.btn {
				border-collapse:collapse;
				background-color: #273b59; /* Cor de fundo do botão */
				border-spacing:0;
				padding:0;
				text-align:left;
				vertical-align:top;
				width: 160px; /* Configura a largura do botão */
			}

			table.btn td {
				text-align: center;
				padding-top: 10px;
				padding-bottom: 10px;
			}

			table.header {
				background-color: #273b59; /* Cor de fundo do cabeçalho */
				width: 580px;
			}

			table.header td{
				padding-top: 15px;
				padding-bottom: 15px;
			}

			img.destaque {
				width: 580px;
			}

			span.date {
				color: #393230;
			}

			@media only screen and (max-width: 480px) {
				body {
					background-color: #ffffff;
				}

				h1 {
					font-size: 1.4rem;
				}

				img.destaque {
					width: 100%;
				}

				table.container {
					width: 100%;
				}
				table.content {
					width: 100%;
				}

				table.header {
					width: 100%;
				}

				table.header td{
					padding-left: 5px;
					padding-right: 5px;
				}
			}
		</style>

		<!--[if gte mso 9]>
			<style type='text/css'>

			</style>
		<![endif]-->
		
		
</head>
             <body>
<center>
	<br>
	<table class='header'>
		<tr>
			<td>
				<center>
				<table class='content' >
					<tr>
						<td>
							<table style='width: 100%; margin: 0 auto;'>
									<tr>
										<td>
											<a href='https://www.firjan.com.br' target='_blanck'> <img src='https://www.firjan.com.br/news/email-mkt/default/logo/iel/firjan-iel-preferencial-branca.png' alt='Logo Firjan' border='0' style='display: block;'></a>
										</td>
										<td style='width: 150px;' class='td-social-icons'>
											<table role='presentation' aria-hidden='true' cellspacing='0' cellpadding='0' border='0' width='100%'>
												<tr>
													<td style='text-align: right;'>
														<a href='https://www.facebook.com/firjanoficial/' target='_blank' class='ico-social' style='width:16px; display: inline-block;'><img src='https://www.firjan.com.br/news/email-mkt/default/icones/branco/ic-facebook.png' style='display: inline-block; width: 16px;'></a>
														&nbsp;
														<a href='https://twitter.com/firjan' target='_blank' class='ico-social' style='width:16px; display: inline-block;'><img src='https://www.firjan.com.br/news/email-mkt/default/icones/branco/ic-twitter.png' style='display: inline-block; width: 16px;' ></a>
														&nbsp;
														<a href='https://www.youtube.com/firjanoficial' target='_blank' class='ico-social' style='width:16px; display: inline-block;'><img src='https://www.firjan.com.br/news/email-mkt/default/icones/branco/ic-youtube.png' style='display: inline-block; width: 16px;'></a>
														&nbsp;
														<a href='https://www.linkedin.com/company/firjan/' target='_blank' class='ico-social' style='width:16px; display: inline-block;'><img src='https://www.firjan.com.br/news/email-mkt/default/icones/branco/ic-linkedin.png' style='display: inline-block; width: 16px;'></a> 
													</td>
												</tr>
											</table>
										</td>
									</tr>
							</table>
						</td>
					</tr>
				</table>
				</center>
			</td>
		</tr>
	</table>
	<!-- Imagem de destaque -->
	<img src='https://www.firjan.com.br/news/email-mkt/2022/0328-futuros-possiveis/header.png' style='display: block;' class='destaque'>
	<table class='container'>
		<tr>
			<td>
				<center>
					<!-- Conteúdo -->
					<br />
					<table class='content'>
						<tr>
							<td>
								<p> <b><em>Olá $firstName! </em> </b> <br>
								  <br>
								  ☑️ Sua inscrição para participar das palestras e painéis do <strong>Festival Futuros Possíveis</strong> 2022 foi concluída com sucesso!</p>
							  <p>Sua inscrição também é válida para participar da <strong>Experiência Imersiva, nos dias 25 e 26/11</strong>, com vagas disponibilizadas por ordem de chegada.</p>
							  <p>Para participar das palestras e painéis, acesse o site, procure o botão login e insira seu usuário <b>$email</b> e sua senha <b>$senha</b></p>
							  <p><a href='https://futurospossiveis.casafirjan.com.br/sites/default/files/2022-08/FFP2022.ics'>Clique aqui</a><strong> e salve o evento na sua agenda.&nbsp;</strong><br>
						      </p>
<p>Em breve, divulgaremos a programação completa do Festival, além das <strong>inscrições para as oficinas</strong> desse ano.&nbsp;</p>
							  <p>Até breve! </p>
</body>
                </html>
				
                ";

                // $template = file_get_contents($module_path.'/email-inscricao.html');
                // $template = str_replace('{{nome}}',$firstName,$template);
                sendMail($to, $subject, $body);
            }

            return new JsonResponse($mgs);
        }
    }

    public function experienciaImersiva(){

        $param = \Drupal::request()->request->all();

        if($param){

            // var_dump($param['ExperienciaImersiva']);die;
            $idsEmail = \Drupal::entityQuery('node')
                        ->condition('type','inscritos_experiencia_imersiva')
                        ->condition('field_e_mail', $param['Email'])
                        ->range(0, 1)
                        ->execute();

            if(!empty($idsEmail)){
                $mgs = ['error' => 'Email já inscrito.'];
            } else if(!isset($param['ExperienciaImersiva']) || empty($param['ExperienciaImersiva'])){
                $mgs = ['error' => 'Selecione pelo menos um dia de Experiencia Imersiva.'];
            } else {

                $EI = array();
                foreach ( explode(',',$param['ExperienciaImersiva']) as &$value) {
                    $EI[]['value'] = $value;
                }

                $user = Node::create(array(
                    'type'        => 'inscritos_experiencia_imersiva',
                    'title'       => $param['NomeCompleto'] . ' - ' . date('d/m/Y H:i'),
                    'field_nome' => ['value' => $param['NomeCompleto']],
                    'field_e_mail' => ['value' => $param['Email']],
                    'field_nacionalidade' => ['value' => $param['Nacionalidade']],
                    'field_cpf' => ['value' => $param['CPF']],
                    'field_passaporte' => ['value' => $param['Passaporte']],
                    'field_nome_empresa' => ['value' => $param['NomeEmpresa']],
                    'field_cnpj' => ['value' => $param['CNPJ']],
                    'field_experiencia_imersiva' => $EI,
                ));
                $user->save();



                // $op = 'register_no_approval_required';
                // _user_mail_notify($op, $user);

                $mgs = ['sucess' => 'Dados salvos com sucesso.'];

                // Envio do eail de boas vindas
                $NomeCompleto = explode(' ',$param['NomeCompleto']);
                $firstName = $NomeCompleto[0];
                $to = $param['Email'];
                $email = $param['Email'];
                $senha = $param['Password'];
                $subject = 'Festival Futuros Possíveis 2022 | Confirmação de Inscrição';

                $body = "



<!DOCTYPE html>
<html lang='en' xmlns='https://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
<head>
		<meta charset='utf-8'> <!-- utf-8 works for most cases -->
		<meta name='referrer' content='never'>
		<meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
		<meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
		<meta name='x-apple-disable-message-reformatting'>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
		<title>Firjan</title> <!-- The title tag shows in email notifications, like Android 4.4. -->


</head>
<body style='background-color: #ebeaea;'>
<center>




	<!-- Imagem de destaque -->
	<img src='https://www.firjan.com.br/news/email-mkt/2021/0268-festival-futuros-possiveis/header-prevenda.jpg' style='display: block;width: 580px;' class='destaque'>
	<table class='container' style='border-collapse: collapse;border: 0;width: 580px;background-color: #273b59;'>
		<tr>
			<td>
				<center>
					<!-- Conteúdo -->
					<br><br>
					<table class='content' style='border-collapse: collapse;border: 0;width: 520px;'>
						<tr>
							<td>


								<h2 style='font-size: 1.4rem;color: #ffffff;font-family: Trebuchet MS, Verdana;padding: 0;margin: 0;text-align: center;'>
									Olá, $firstName! Tudo bem?
								</h2>
								<br>
								<p style='font-size: 1rem;color: #ffffff;font-family: Trebuchet MS, Verdana;text-align: center;line-height: 1.5;'>	Obrigada por se inscrever na <b>experiência imersiva</b> do <b>Summit Firjan IEL + Festival Futuros Possíveis 2021.</b>
									<br>
									<br>
									Você receberá, via e-mail cadastrado, <span style='color: #f2a900;'>o link com orientações de acesso ao ambiente virtual.</span>
									<br>
                                    <br>
                                    <br>
                                Nos vemos lá!
								</p>
								<h2 style='font-size: 1.4rem;color: #ffffff;font-family: Trebuchet MS, Verdana;padding: 0;margin: 0;text-align: center;'>
								Equipe Firjan IEL
								</h2>
								<br>

							</td></tr></table></center>

								<br>

								<br><br>&nbsp;
							</td>
						</tr>
					</table>
				</center>




<center style='text-align: center'>	<p style='font-size: 12px;color: #9c9897;text-align: center;font-family: Trebuchet MS, Verdana;line-height: 1.5;'><i>Caso você não queira mais receber informações da Firjan e suas instituições, <br><a href='mailto:descadastrar@firjan.com.br?subject=Não quero mais receber informações' style='color: #003bd1;'>clique aqui</a>. Caso você queira exercer os seus direitos referentes aos seus dados <br>pessoais, por favor, entre em contato com: <a href='mailto:dpo@firjan.com.br?subject=Direitos referentes aos meus dados pessoais' style='color: #003bd1;'>dpo@firjan.com.br</a>.</i></p></center>


<br><br>&nbsp;</body>
</html>

                ";

                sendMail($to, $subject, $body);
            }

            return new JsonResponse($mgs);
        }
    }


    public function cadastro(){
        //Palestrantes
        /*
        $viewOficinas = GM5Views::getViewArg('user_mail','lista');

        foreach($viewOficinas['items'] as $k => $oficina){
          $oficinaDia = $oficina->_entity;
          $bg = $oficinaDia->toArray();

          //print_r($oficinaDia->toArray()); die;
          if(isset($bg['name'][0]['value']) && !empty($bg['name'][0]['value'])){
              //$op = 'register_no_approval_required';
          //_user_mail_notify($op, $oficinaDia);
          //$oficinaDia->field_notificado->value = 'sim';
          //$oficinaDia->save();
          }


        }
        */
    }


    public function oficinas(){


        $msg = array();
        if(isset($_FILES) && !empty($_FILES)){
            $uploadName = rand(0, 9999).'_'.basename($_FILES['comprovante']['name']);
            $uploaddrupal = \Drupal::service('file_system')->realpath(file_default_scheme() . '://').'/comprovantes/'.$uploadName;

            if (move_uploaded_file($_FILES['comprovante']['tmp_name'], $uploaddrupal)) {
                // $msg['file'] = 'Arquivo válido e enviado com sucesso.';
            } else {
                // $msg['file'] = 'Possível falha de upload de arquivo.';
                $msg['error'][] = 'Possível falha de upload de arquivo.';
            }

            $file = File::create([
                'filename' => $uploadName,
                'uri' => 'public://comprovantes/'.$uploadName,
                'status' => 1,
            ]);
            $file->save();
        }


        $file = isset($file) ? $file->id() : '';

        if(!isset($_POST['nome']) || empty($_POST['nome']) || $_POST['nome'] == 'undefined' || $_POST['nome'] == NULL){
            $msg['error'][] = 'O preenchimento do Nome é obrigatório';
        }
        if(!isset($_POST['email']) || empty($_POST['email']) || $_POST['email'] == 'undefined' || $_POST['email'] == NULL){
            $msg['error'][] = 'O preenchimento do E-mail é obrigatório';
        }
        if(!isset($_POST['cpf']) || empty($_POST['cpf']) || $_POST['cpf'] == 'undefined' || $_POST['cpf'] == NULL){
            $msg['error'][] = 'O preenchimento do CPF é obrigatório';
        }
        if(!isset($_POST['nascimento']) || empty($_POST['nascimento']) || $_POST['nascimento'] == 'undefined' || $_POST['nascimento'] == NULL){
            $msg['error'][] = 'O preenchimento da Data de Nascimento é obrigatório';
        }
        if(!isset($_POST['endereco']) || empty($_POST['endereco']) || $_POST['endereco'] == 'undefined' || $_POST['endereco'] == NULL){
            $msg['error'][] = 'O preenchimento do Endereço é obrigatório';
        }

        if(!isset($_POST['associado']) || empty($_POST['associado']) || $_POST['associado'] == 'undefined' || $_POST['associado'] == NULL){
            $msg['error'][] = 'O preenchimento do campo Associado à Firjan é obrigatório';
        }
        if(!isset($_POST['cep']) || empty($_POST['cep']) || $_POST['cep'] == 'undefined' || $_POST['cep'] == NULL){
            $msg['error'][] = 'O preenchimento do CEP é obrigatório';
        }
        if(!isset($_POST['formacao']) || empty($_POST['formacao']) || $_POST['formacao'] == 'undefined' || $_POST['formacao'] == NULL){
            $msg['error'][] = 'O preenchimento da Formação Acadêmica é obrigatório';
        }
        if(!isset($_POST['oficina']) || empty($_POST['oficina']) || $_POST['oficina'] == 'undefined' || $_POST['oficina'] == NULL){
            $msg['error'][] = 'Oficina não selecionada';
        }

        if(empty($msg['error'])){


            // Envio de e-mail
            $NomeCompleto = explode(' ',$_POST['nome']);
            $oficina = $_POST['oficina_nome'];
            $oficinaDia = substr($_POST['oficina_dia'], 0, 2);;
            $firstName = $NomeCompleto[0];
            $to = $_POST['email'];
            $subject = 'Festival Futuros Possíveis 2022 | Confirmação de Inscrição em Oficina';


            $body = "



<!DOCTYPE html>
<html lang='en' xmlns='https://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
<head>
		<meta charset='utf-8'> <!-- utf-8 works for most cases -->
		<meta name='referrer' content='never'>
		<meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
		<meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
		<meta name='x-apple-disable-message-reformatting'>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
		<title>Firjan</title> <!-- The title tag shows in email notifications, like Android 4.4. -->


</head>
<body style='background-color: #ebeaea;'>
<center>
	<br>



	<!-- Imagem de destaque -->
	<img src='https://www.firjan.com.br/news/email-mkt/2021/0268-festival-futuros-possiveis/header-prevenda.jpg' style='display: block;width: 580px;' class='destaque'>
	<table class='container' style='border-collapse: collapse;border: 0;width: 580px;background-color: #273b59;'>
		<tr>
			<td>
				<center>
					<!-- Conteúdo -->
					<br><br>
					<table class='content' style='border-collapse: collapse;border: 0;width: 520px;'>
						<tr>
							<td>


								<h2 style='font-size: 1.4rem;color: #ffffff;font-family: Trebuchet MS, Verdana;padding: 0;margin: 0;text-align: center;'>
									Olá, $firstName! Tudo bem?
								</h2>
								<br>
								<p style='font-size: 1rem;color: #ffffff;font-family: Trebuchet MS, Verdana;text-align: center;line-height: 1.5;'>
									Obrigada por escolher a oficina <b>$oficina</b>, a ser realizada no dia <b>$oficinaDia de novembro</b>, no <b>Summit Firjan IEL + Festival Futuros Possíveis 2021.</b>
                                    <br>
                                    <br>
                                    Entraremos em contato, via e-mail cadastrado, para que você possa <span style='color: #f2a900;'>efetuar o pagamento da oficina escolhida.</span>
                                    <br>
                                    <br>
                                    <br>
                                    Até breve!
								</p>
								<h2 style='font-size: 1.4rem;color: #ffffff;font-family: Trebuchet MS, Verdana;padding: 0;margin: 0;text-align: center;'>
								Equipe Firjan IEL
								</h2>
								<br>

							</td></tr></table></center>

								<br>

								<br><br>&nbsp;
							</td>
						</tr>
					</table>
				</center>





<center style='text-align: center'>	<p style='font-size: 12px;color: #9c9897;text-align: center;font-family: Trebuchet MS, Verdana;line-height: 1.5;'><i>Caso você não queira mais receber informações da Firjan e suas instituições, <br><a href='mailto:descadastrar@firjan.com.br?subject=Não quero mais receber informações' style='color: #003bd1;'>clique aqui</a>. Caso você queira exercer os seus direitos referentes aos seus dados <br>pessoais, por favor, entre em contato com: <a href='mailto:dpo@firjan.com.br?subject=Direitos referentes aos meus dados pessoais' style='color: #003bd1;'>dpo@firjan.com.br</a>.</i></p></center>


<br><br>&nbsp;</body>
</html>

                ";


                sendMail($to, $subject, $body);

            // Salva o node de inscrição
            $node = Node::create([
                'type'        => 'incrito_oficina',
                'title'       => $_POST['nome'] . ' - ' . date('d/m/Y H:i'),
                'field_nome' => ['value' => $_POST['nome']],
                'field_e_mail' => ['value' => $_POST['email']],
                'field_telefone' => ['value' => $_POST['telefone']],
                'field_cpf' => ['value' => $_POST['cpf']],
                'field_nascimento' => ['value' => $_POST['nascimento']],
                'field_endereco_completo' => ['value' => $_POST['endereco']],
                'field_cep' => ['value' => $_POST['cep']],
                'field_formacao_academica' => ['value' => $_POST['formacao']],
                'field_cnpj' => ['value' => $_POST['cnpj']],
                'field_nome_empresa' => ['value' => $_POST['empresa']],
                'field_e_associado_a_firjan' => ['value' => $_POST['associado']],
                'field_comprovante' => ['target_id' => $file],
                'field_oficinas' => $_POST['oficina'],
                'field_eu_aceito_os_termos_de_uso' => ['value' => 1],
            ]);

            $node->save();

        }

        return new JsonResponse($msg);

        }


    public function delete(){
        // $result = \Drupal::entityQuery('node')
        //     ->condition('type', 'relatorio')
        //     ->range(0, 500)
        //     ->execute();
        // // var_dump($result);die;
        // entity_delete_multiple('node', $result);
        // $count = \Drupal::entityQuery('node')
        //     ->condition('type', 'relatorio')
        //     ->execute();
        // var_dump(count($count));
        // die;

        // $user_ids = \Drupal::entityQuery('user')
        // ->condition('uid', [0,1], 'NOT IN')
        // ->range(0, 400)
        // ->execute();
        // entity_delete_multiple('user', $user_ids);
        // $user_ids = \Drupal::entityQuery('user')
        // ->execute();
        // var_dump(COUNT($user_ids));die;


        // print_r($_FILES['comprovante']['name']);
        // $file_storage = \Drupal::entityTypeManager()->getStorage('file');
        // $fids = \Drupal::entityQuery('file')->condition('status', 1)->execute();
        // $file_usage = \Drupal::service('file.usage');
        // foreach ($fids as $fid) {
        //   $file = \Drupal\file\Entity\File::load($fid);
        //   $usage = $file_usage->listUsage($file);
        //   if(count($usage) == 0) {
        //     $file->delete();
        //   }
        // }
        // die();
    }


}

