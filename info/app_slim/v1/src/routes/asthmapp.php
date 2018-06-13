<?php

namespace App\v1\src\routes;

use App\v1\src\models\asthmapp as Models;
use App\v1\src\libs\Response;
use App\v1\src\libs\Asthmapp\ComposerQuizPdf;
use App\v1\src\libs\Asthmapp\ComposerReportPdf;

// Routes
$app->group('/asthmapp', function(){

	//Create test group
	$this->post('/creategroup', function($req, $res, $args){
		//Create response object
		$response = new Response();
		//Create group model
		$model_group = new Models\Asthmapp_group_model($this->db);
		//get data post
		$json_data = $req->getParsedBody();

		if(isset($json_data['data'])){
			$data = json_decode($json_data['data'], true);
			//verifyrequired params
			$validation = $this->validator->verifyRequiredParams(array('email', 'date_start', 'date_finish'), $data);

			if($validation->response){ //Validation params ok
				//get insert response
				$resp_insert_group = $model_group->insertOrUpdate($data);
				if($resp_insert_group->response){ //Insert ok
					//build data param
					$data_pwd = array(
						'group_id' => $resp_insert_group->result,
						'pwd' => $this->pwd_generator
						);

					$model_pwd = new Models\Asthmapp_group_pwd_model($this->db);
					//get response insert pwd
					$resp_insert_pwd = $model_pwd->insertOrUpdate($data_pwd);

					if($resp_insert_pwd->response){
						$response->setResponse(true);
						$response->result = $data_pwd['group_id'];
					}else{
						$response = $resp_insert_pwd;
					}

				}else{ //Insert error
					$response = $resp_insert_group;
				}
				
			}else{ // Error validation params
				//build response error validation
				$response = $validation;
			}
		}else{
			//build response missing data post
			$response->setResponse(false, "Missing data post");
		}
		
		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});


	//Consult active groups
	$this->post('/getactivegroups', function($req, $res, $args){
		//Create response object
		$response = new Response();
		//Create group Model
		$model = new Models\Asthmapp_group_model($this->db);
		//get data post
		$json_data = $req->getParsedBody();

		if(isset($json_data['data'])){
			$data = json_decode($json_data['data'], true);

			$validation = $this->validator->verifyRequiredParams(array('email'), $data);

			if($validation->response){ //Validation params ok
				//Build response query
				$response = $model->getAllByEmail($data['email']);
			}else{ //Error validation params
				//build response error validation
				$response = $validation;
			}
		}else{
			//Build response missing data post
			$response->setResponse(false, "Missing data post");
		}

		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});

	$this->post('/createtest', function($req, $res, $args){
		//path files
		$path = "asthmapp_files/";
		//Create response object
		$response = new Response();
		//Create mailer object
		$mailer = $this->mailSender;
		//Create pdf object
		$pdf = $this->pdf;
		//Create render pdf
		$composerPdf = new ComposerQuizPdf($pdf);
		//Create group model
		$model_group = new Models\Asthmapp_group_model($this->db);
		//Create test model
		$model_test = new Models\Asthmapp_test_model($this->db);
		//Create pwd group model
		$model_pwd = new Models\Asthmapp_group_pwd_model($this->db);
		//get data post
		$json_data = $req->getParsedBody();

		//Verify data post
		if(isset($json_data['data'])){
			$data = json_decode($json_data['data'], true);
			//Verify required params
			$validation = $this->validator->verifyRequiredParams(array('name', 'document', 'email', 'result', 'date_test', 'device_id', 'remote_id'), $data);

			//process validation
			if($validation->response){
				$resp_group_id = $model_group->getGroupByDate($data['date_test']);

				if($resp_group_id->response){
					//append data
					$data['group_id'] = $resp_group_id->result;
					//Insert test
					$resp_insert_test = $model_test->insertOrUpdate($data);
					
					//Validate inserted record
					if($resp_insert_test->response){
						//Get pwd group
						$resp_pwd_group = $model_pwd->getPwdByGroup($data['group_id']);

						if($resp_pwd_group->response){
							//generate and send pdf
							$pdf_data = json_decode($data['result'], true);
							$pdf_data["student"] = array(
								"name" => $data["name"],
								"document" => $data["document"],
								"pwd" => $resp_pwd_group->result
								);

							$resp_pdf = $composerPdf->render($pdf_data, $path);
							if($resp_pdf->response){
								//Params mail
								$from = "asthmapp@gibicgroup.com";
								$to = $data['email'];
								$message = "Señor estudiante,<br><br>Adjunto a este correo encontrará la nota del quiz que acaba de presentar en la aplicación Asthmapp.<br>La clave para abrir este documento será enviada por su profesor más adelante.<br><br>Asthmapp es una aplicación del Grupo de Investigación en Bioinstrumentación e Ingeniería Clínica (GIBIC)  con el apoyo del Centro de Información y Estudio de Medicamentos y Tóxicos (CIEMTO), Universidad de Antioquia.<br><br>

								<br>Por favor no responder a este mensaje, fue generado automaticamente<br><br>

								grupogibic@udea.edu.co<br>
								http://www.gibicgroup.com<br>
								http://www.udea.edu.co";
								$subject = "Resultados Quiz Asthmapp";

								$url_file = $path . $data['document'] . ".pdf";

								$mailer->setParams($from, $to, $subject, $message);
								$mailer->attachFile($url_file);

								//Send mail
								$send_result = $mailer->send();

								if($send_result){
									$response->setResponse(true);
									$response->result = "Test result sent.";
								}else{
									$response->setResponse(false, "Failed to send test result.");
								}
							}else{	
								$response = $resp_pdf;
							}
						}else{
							$response = $resp_pwd_group;
						}
					}else{
						$response = $resp_insert_test;
					}
				}else{
					$response = $resp_group_id;
				}

			}else{// Error validation params
				//build response error validation
				$response = $validation;
			}
			
		}else{
			//build response missing data post
			$response->setResponse(false, "Missing data post");
		}

		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});

	$this->post('/sendpwd', function($req, $res, $args){
		//Create response object
		$response = new Response();
		//Create mailer object
		$mailer = $this->mailSender;
		//Create pwd group model
		$model_pwd_group = new Models\Asthmapp_group_pwd_model($this->db);
		//Create test model
		$model_test = new Models\Asthmapp_test_model($this->db);
		//get post data
		$json_data = $req->getParsedBody();
		//Verify fata post
		if(isset($json_data['data'])){
			$data = json_decode($json_data['data'], true);
			//Verify required params
			$validation = $this->validator->verifyRequiredParams(array('group_id'), $data);

			//process validation
			if($validation->response){
				//Get group tests
				$resp_test_group = $model_test->getAllByGroup($data['group_id']);
				//Get pwd group
				$resp_pwd_group = $model_pwd_group->getPwdByGroup($data['group_id']);

				//Validate responses
				if($resp_test_group->response){//consult group tests
					if($resp_pwd_group->response){//consult pwd group
						//Get data
						$tests = $resp_test_group->result;
						$pwd = $resp_pwd_group->result;

						$from = "asthmapp@gibicgroup.com";
						$to = "";
						foreach ($tests as $test) {
							$to .= $test['email'] . ";";
						}

						$message = "Señor estudiante,<br><br>La clave asignada para abrir el documento de resultados es: " . $pwd . "<br><br>Asthmapp es una aplicación del Grupo de Investigación en Bioinstrumentación e Ingeniería Clínica (GIBIC)  con el apoyo del Centro de Información y Estudio de Medicamentos y Tóxicos (CIEMTO), Universidad de Antioquia.<br><br>

						<br>Por favor no responder a este mensaje, fue generado automaticamente<br><br>
						grupogibic@udea.edu.co<br>
						http://www.gibicgroup.com<br>
						http://www.udea.edu.co";
						$subject = "Resultados Quiz Asthmapp";

						$mailer->setParams($from, $to, $subject, $message);

						$send_result = $mailer->send();

						if($send_result){
							$response->setResponse(true);
							$response->result = "Password sent.";
						}else{
							$response->setResponse(false, "Failed to send password.");
						}
					}else{//Group pwd
						$response = $resp_pwd_group;
					}
				}else{//Group tests
					$response = $resp_test_group;
				}

			}else{// Error validation params
				//build response error validation
				$response = $validation;
			}
		}else{
			//build response missing data post
			$response->setResponse(false, "Missing data post.");
		}

		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});

	$this->post('/createreport', function($req, $res, $args){
		//path files
		$path = "asthmapp_files/";
		//Create response object
		$response = new Response();
		//Create mailer object
		$mailer = $this->mailSender;
		//Create pdf object
		$pdf = $this->pdf;
		//Create render pdf
		$composerPdf = new ComposerReportPdf($pdf);
		//Create group model
		$model_group = new Models\Asthmapp_group_model($this->db);
		//Create test model
		$model_test = new Models\Asthmapp_test_model($this->db);
		//get data post
		$json_data = $req->getParsedBody();

		//Verify data post
		if(isset($json_data['data'])){
			$data = json_decode($json_data['data'], true);
			//Verify required params
			$validation = $this->validator->verifyRequiredParams(array('group_id'), $data);

			if($validation->response){ //Validation params ok
				
				//Get group tests
				$resp_test = $model_test->getAllByGroup($data['group_id']);
				//Get pwd group
				$resp_group = $model_group->getAllById($data['group_id']);

				if($resp_test->response && $resp_group->response){
					//Get tests
					$tests = $resp_test->result;
					//Get group
					$group = $resp_group->result[0]; // Get row 0

					$pdf_data = array(
						"tests" => $tests,
						"group" => $group
						);

					$date_group = substr($group["date_start"], 0,10);
					$date_group = str_replace("-", "_", $date_group);

					$path_file = $path . "report_group_" . $group["id"] . "_" . $date_group . " .pdf";

					$resp_pdf = $composerPdf->render($pdf_data, $path_file);
					if($resp_pdf->response){
						//Params mail
						$from = "asthmapp@gibicgroup.com";
						$to = $group["email"];
						$message = "Señor profesor,<br><br>Adjunto a este correo encontrará el reporte de notas para el quiz Asthmapp presentado por los estudiantes la fecha $date_group.Los estudiantes ya han recibido un correo con la clave para acceder a sus reportes de quiz.<br><br>Asthmapp es una aplicación del Grupo de Investigación en Bioinstrumentación e Ingeniería Clínica (GIBIC)  con el apoyo del Centro de Información y Estudio de Medicamentos y Tóxicos (CIEMTO), Universidad de Antioquia.<br><br>

						<br>Por favor no responder a este mensaje, fue generado automaticamente<br><br>

						grupogibic@udea.edu.co<br>
						http://www.gibicgroup.com<br>
						http://www.udea.edu.co";
						$subject = "Resultados Quiz Asthmapp";

						$mailer->setParams($from, $to, $subject, $message);
						$mailer->attachFile($path_file);

						//Send mail
						$send_result = $mailer->send();

						if($send_result){
							$response->setResponse(true);
							$response->result = "Report sent.";
						}else{
							$response->setResponse(false, "Failed to send report.");
						}
					}else{	
						$response = $resp_pdf;
					}
				}else{
					$response->setResponse(false, "Error getting data.");
				}
				
			}else{ // Error validation params
				//build response error validation
				$response = $validation;
			}
		}else{
			//build response missing data post
			$response->setResponse(false, "Missing data post.");
		}

		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});

$this->post('/gettests', function($req, $res, $args){
		//Create response object
		$response = new Response();
		//Create test model
		$model_test = new Models\Asthmapp_test_model($this->db);
		//get data post
		$json_data = $req->getParsedBody();

		//Verify data post
		if(isset($json_data['data'])){
			$data = json_decode($json_data['data'], true);
			//Verify required params
			$validation = $this->validator->verifyRequiredParams(array('group_id'), $data);

			if($validation->response){ //Validation params ok
				
				//Get group tests
				$response = $model_test->getAllByGroup($data['group_id']);
				
			}else{ // Error validation params
				//build response error validation
				$response = $validation;
			}
		}else{
			//build response missing data post
			$response->setResponse(false, "Missing data post.");
		}

		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});


	$this->get('/test', function($req, $res, $args){
		$res->getBody()->write("Service OK");
		return $res;
	});
});

?>