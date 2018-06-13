<?php

namespace App\v1\src\routes;

use App\v1\src\models\asthmapp as Models;
use App\v1\src\libs\Response;
use App\v1\src\libs\Asthmapp\ComposerQuizPdf;
use App\v1\src\libs\Asthmapp\ComposerReportPdf;

// Routes
$app->group('/asthmapp', function(){

	//group groups
	$this->group('/group', function(){
		//Create test group
		$this->post('/new', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create group model
			$model_group = new Models\Asthmapp_group_model($this->db);
			//get data post
			$json_data = $req->getParsedBody();

			if(isset($json_data['data'])){
				$data = json_decode($json_data['data'], true);
				//verifyrequired params
				$validation = $this->validator->verifyRequiredParams(array('teacher_name', 'label', 'email', 'date_start', 'date_finish'), $data);

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
							$data_result_new_group = array(
								"group_id" => $data_pwd['group_id']
								);
							$response->setResponse(true);
							$response->result = $data_result_new_group;
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
		$this->post('/active', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create group Model
			$model = new Models\Asthmapp_group_model($this->db);

			$response = $model->getAllActive();

			$res = $res->withHeader('Content-type', 'application/json');
			$res->getBody()
				->write(json_encode($response));

			return $res;
		});

						//Consult all groups
		$this->post('/all', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create group Model
			$model = new Models\Asthmapp_group_model($this->db);

			$response = $model->getAll();

			$res = $res->withHeader('Content-type', 'application/json');
			$res->getBody()
				->write(json_encode($response));

			return $res;
		});

		//close a group
		$this->post('/close', function($req, $res, $args){
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
			//Create group Model
			$model_group = new Models\Asthmapp_group_model($this->db);
			//Create test model
			$model_test = new Models\Asthmapp_test_model($this->db);
			//Create pwd group model
			$model_pwd_group = new Models\Asthmapp_group_pwd_model($this->db);
			//get data post
			$json_data = $req->getParsedBody();

			if(isset($json_data['data'])){
				$data = json_decode($json_data['data'], true);

				$validation = $this->validator->verifyRequiredParams(array('id', 'date_finish'), $data);

				if($validation->response){ //Validation params 
					//consult group tests
					$resp_test = $model_test->getAllByGroup($data['id']);
					//consult pwd group
					$resp_pwd_group = $model_pwd_group->getPwdByGroup($data['id']);
					//consult group
					$resp_group = $model_group->getById($data['id']);

					//validate responses
					if(($resp_test->response) && ($resp_pwd_group->response) && ($resp_group->response)){
						//Get tests
						$tests = $resp_test->result;
						//Get group
						$group = $resp_group->result[0];
						//Get pwd
						$pwd = $resp_pwd_group->result;

						//get and format date group
						$date_group = substr($group["date_start"], 0,10);
						$date_group = str_replace("-", "_", $date_group);

						$pdf_data = array(
							"tests" => $tests,
							"group" => $group
							);

						// Create path file for report
						$path_file = $path . "report_group_" . $group["id"] . "_" . $date_group . " .pdf";
						
						$resp_pdf = $composerPdf->render($pdf_data, $path_file);

						if($resp_pdf->response){
							//Params mail
							$from = "asthmapp@gibicgroup.com";
							$subject = "Resultados Quiz Asthmapp";

							$to = $group["email"];
							$message = "Señor profesor,<br><br>Adjunto a este correo encontrará el reporte de notas para el quiz Asthmapp presentado por los estudiantes la fecha $date_group.Los estudiantes ya han recibido un correo con la clave para acceder a sus reportes de quiz.<br><br>Asthmapp es una aplicación del Grupo de Investigación en Bioinstrumentación e Ingeniería Clínica (GIBIC)  con el apoyo del Centro de Información y Estudio de Medicamentos y Tóxicos (CIEMTO), Universidad de Antioquia.<br><br>

							<br>Por favor no responder a este mensaje, fue generado automaticamente<br><br>

							grupogibic@udea.edu.co<br>
							http://www.gibicgroup.com<br>
							http://www.udea.edu.co";
							

							$mailer->setParams($from, $to, $subject, $message);
							$mailer->attachFile($path_file);

							//Send mail
							$send_result_report = $mailer->send();

							$to = "";
							if($send_result_report){
								foreach ($tests as $test) {
									$to .= $test['email'] . ",";
								}

								$message = "Señor estudiante,<br><br>La clave asignada para abrir el documento de resultados es: " . $pwd . "<br><br>Asthmapp es una aplicación del Grupo de Investigación en Bioinstrumentación e Ingeniería Clínica (GIBIC)  con el apoyo del Centro de Información y Estudio de Medicamentos y Tóxicos (CIEMTO), Universidad de Antioquia.<br><br>

								<br>Por favor no responder a este mensaje, fue generado automaticamente<br><br>
								grupogibic@udea.edu.co<br>
								http://www.gibicgroup.com<br>
								http://www.udea.edu.co";

								$mailer->setParams($from, $to, $subject, $message);

								$send_result_pwd = $mailer->send();

								if($send_result_pwd){
									//Build response query
									$resp_close = $model_group->close($data);

									if($resp_close->response){
										$response = $resp_close;
									}else{
										$response->setResponse(false, "Failed to close group. Report sent. Password sent.");
									}
								}else{
									$response->setResponse(false, $to);
								}
							}else{
								$response->setResponse(false, "Failed to send report. The password has not been sent.");
							}
						}else{	
							$response = $resp_pdf;
						}
					}else{
						$response->setResponse(false, "Error getting data. The password has not been sent. The report has not been generated.");
					}

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

		//delete a group
		$this->post('/delete', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create group Model
			$model = new Models\Asthmapp_group_model($this->db);
			//get data post
			$json_data = $req->getParsedBody();

			if(isset($json_data['data'])){
				$data = json_decode($json_data['data'], true);

				$validation = $this->validator->verifyRequiredParams(array('id'), $data);

				if($validation->response){ //Validation params ok
					//Build response query
					$response = $model->delete($data['id']);
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
	}); 


	$this->group('/test', function(){
		//Create a new test
		$this->post('/new', function($req, $res, $args){
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
				$validation = $this->validator->verifyRequiredParams(array('name', 'document', 'email', 'result', 'date_test', 'device_id', 'remote_id', 'group_id', 'status'), $data);

				//process validation
				if($validation->response){
					$resp_group_to_save = $model_group->getGroupToSave($data['group_id'],$data['date_test']);

					if($resp_group_to_save->response){
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
								$pdf_data["status"] = $data["status"];

								$path_file = $path . $data["document"] . ".pdf";

								$resp_pdf = $composerPdf->render($pdf_data, $path_file);
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
										$data_result_new_test = array(
											"success" => "1"
											);
										$response->setResponse(true);
										$response->result = $data_result_new_test;
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
						$response = $resp_group_to_save;
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

		$this->post('/all', function($req, $res, $args){
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
				$validation = $this->validator->verifyRequiredParams(array('id'), $data);

				if($validation->response){ //Validation params ok
					
					//Get group tests
					$response = $model_test->getAllByGroup($data['id']);
					
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
	});
});

?>