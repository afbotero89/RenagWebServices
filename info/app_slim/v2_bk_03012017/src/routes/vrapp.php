<?php

namespace App\v2\src\routes;

use App\v2\src\models\vrapp as Models;
use App\v2\src\libs\Response;
use App\v2\src\libs\Message;

// Routes
$app->group('/vrapp', function(){
	//group doctors
	$this->group('/doctor', function(){

		//Create a new doctor
		$this->post('/new', function($req, $res, $args){
			//Create a response object
			$response = new Response();
			// Create a message Object
			$message = new Message();
			// Create doctor model
			$model = new Models\Vrapp_doctor_model($this->db);
			//get data post
			$json_data = $req->getParsedBody();

			//Validate if data is set
			if(isset($json_data['data'])){
				//decode data post
				$data = json_decode($json_data['data'], true);
				//Verify required params
				$required_params = $this->validator->verifyRequiredParams(array('name', 'lastname','email','password'), $data);

				if($required_params->response){
					//Validate emaio
					$email_validation = $this->validator->validateEmail($data['email']);

					if($email_validation->response){

						//insert record
						$response = $model->insertOrUpdate($data);

					}else{
						$response = $email_validation;
					}
				}else{
					$response = $required_params;
				}
			}else{
				//Build response missing data post
				$response->setResponse(false, $message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});

		//Consult all doctors
		$this->post('/all', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create doctor Model
			$model = new Models\Vrapp_doctor_model($this->db);

			$response = $model->getAll();

			return $response->writeResponse($res, $response);
		});

		//delete a doctor
		$this->post('/delete', function($req, $res, $args){
			// create response object
			$response = new Response();
			//Create a message object
			$message = new Message();
			//Create doctor model object
			$model = new Models\Vrapp_doctor_model($this->db);
			//get json data post
			$json_data = $req->getParsedBody();
			
			//validate if data is set
			if(isset($json_data['data'])){
				//decode data post
				$data = json_decode($json_data['data'], true);
				//Verify required params
				$required_params = $this->validator->verifyRequiredParams(array('id'), $data);

				if($required_params->response){
					$response = $model->delete($data['id']);
				}else{
					$response = $required_params;
				}
			}else{
				//build response error data post
				$response->setResponse(false, $message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});
	});

	//Patient group
	$this->group('/patient', function(){

		$this->post('/new', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create a message object
			$message = new Message();
			//Create model patient object
			$model = new Models\Vrapp_patient_model($this->db);
			// get json data post
			$json_data = $req->getParsedBody();
			//verify if data is set
			if(isset($json_data['data'])){
				//decode json data
				$data = json_decode($json_data['data'], true);
				//verify required required_params
				$required_params = $this->validator->verifyRequiredParams(array('name','lastname','document','born_date','gender','medical_condition','drugs','sedentary','smoking','diabetes','doctor_id','device_id','remote_id'), $data);

				if($required_params->response){
					$response = $model->insertOrUpdate($data);
				}else{
					$response = $required_params;
				}
			}else{
				$response->setResponse(false, $message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});

		//Consult all patients for a doctor
		$this->post('/all', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create mesage object
			$message = new Message();
			//Create patient Model
			$model = new Models\Vrapp_patient_model($this->db);
			//Get json data post
			$json_data = $req->getParsedBody();
			//Verify if data is set
			if(isset($json_data['data'])){
				//decode json data
				$data = json_decode($json_data['data'], true);
				//Validate required params
				$required_params = $this->validator->verifyRequiredParams(array('doctor_id'), $data);

				if($required_params->response){
					$response = $model->getAllByDoctor($data['doctor_id']);
				}else{
					$response = $required_params;
				}
			}else{
				$response->setResponse(false, $message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});
		
		$this->post('/delete', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create message object
			$message = new Message();
			//Create model patient object
			$model = new Models\Vrapp_patient_model($this->db);
			// Get json data post
			$json_data = $req->getParsedBody();
			// verify if data is set
			if(isset($json_data['data'])){
				//decode json data
				$data = json_decode($json_data['data'], true);
				// validate required params
				$required_params = $this->validator->verifyRequiredParams(array('id'), $data);

				if($required_params->response){
					$response = $model->delete($data['id']);
				}else{
					$response = $required_params;
				}
			}else{
				$response->setResponse(false, $message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});
	});

	//test group
	$this->group('/test', function(){

		//Consult all test
		$this->post('/all', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create test Model
			$model = new Models\Vrapp_test_model($this->db);

			$response = $model->getAll();

			return $response->writeResponse($res, $response);
		});

		//create a new test
		$this->post('/new', function($req, $res, $args){
			//create response object
			$response = new Response();
			//create message object
			$message = new Message();
			//create test model object
			$model = new Models\Vrapp_test_model($this->db);
			// get json data
			$json_data = $req->getParsedBody();
			//verify if data is set
			if(isset($json_data['data'])){
				//decode json data
				$data = json_decode($json_data['data'], true);

				//verify required params
				$required_params = $this->validator->verifyRequiredParams(
					array(
						'occlusion_time',
						'recovery_time',
						'vr_index',
						'stop_pressure',
						'sys_pressure',
						'dia_pressure',
						'avg_pressure',
						'pulse_pressure',
						'init_amp',
						'min_amp',
						'max_amp',
						'final_amp',
						'rising_time',
						'date_test',
						'abdom_circ',
						'chart_data',
						'final_state',
						'patient_id',
						'device_id',
						'remote_id'
						), $data);

				if($required_params->response){
					$response = $model->insertOrUpdate($data);
				}else{	
					$response = $required_params;
				}
			}else{
				$response->setResponse(false, $this->message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});

		//delete test
		$this->post('/delete', function($req, $res, $args){
			//Create response object
			$response = new Response();
			//Create message object
			$message = new Message();
			//Create a model test object
			$model = new Models\Vrapp_test_model($this->db);
			//get json data post
			$json_data = $req->getParsedBody();

			// verify if data is set
			if(isset($json_data['data'])){
				//decode json data
				$data = json_decode($json_data['data']);
				//verify required params
				$required_params = $this->validator->verifyRequiredParams(array('id'), $data);

				if($required_params->response){
					$response = $model->delete($data['id']);
				}else{	
					$response = $required_params;
				}
			}else{
				$response->setResponse(false, $this->message->getError(9));
			}

			return $response->writeResponse($res, $response);
		});
	});
});