<?php

namespace App\v2\src\routes;

use App\v2\src\models\pressure as Models;
use App\v2\src\libs\Response;
use App\v2\src\libs\Validation;

// Routes
$app->group('/pressure', function(){

	$this->post('/login', function($req, $res, $args){
		//Middleware response
		$response = $this->login;
		//Validate result
		if($response->response){
			$this->logger->addInfo("Query login Ok");
			// Validate result login
			if(!count($response->result)){
				$this->logger->addInfo("Login Failed");

				$response = new Response();
				$response->setResponse(false, "User validation failed");
			}else{
				$this->logger->addInfo("Login OK");
			}
		}else{
			$this->logger->addInfo("Query login error: " . $response->message);
		}
		//make response
		$res->getBody()
			->write(json_encode($response));

		return $res;
	});

	//Meassures routes
	$this->group('/meassure', function(){
		//return all $this->pdfs
		$this->get('/all', function($req, $res, $args){
			// Create meassures model
			$model = new Models\Pressure_meassure_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res->getBody()
				->write($response);

			return $res;
		});
		//Add new meassure
		$this->post('/save', function($req, $res, $args){
			// Create meassures model
			$model = new Models\Pressure_meassure_model($this->db);
			// get data post
			$json_data = $req->getParsedBody();
			$data = json_decode($json_data['data'], true);

			//validate fields
			$validation_result = $this->validator->verifyRequiredParams(array('patient_id','data_ac','data_dc','avg','sys','dia','time','date'), $data);

			if($validation_result->response){
				// Query database and json encode
				$response = json_encode($model->insertOrUpdate($data));
				//make response
				$res->getBody()
					->write($response);
			}else{

				$response = json_encode($validation_result);
				$res->getBody()
					->write($response);
			}

				
			return $res;
		});

		$this->post('/delete/{id}', function($req, $res, $args){
			//create meassure model
			$model = new Models\Pressure_meassure_model($this->db);
			//get meassure id
			$id = $req->getAttribute('id');
			//Query and encode
			$response = json_encode($model->delete($id));
			//make response
			$res->getBody()
				->write($response);

			return $res;
		});

		$this->get('/patient/{id}', function($req, $res, $args){
			//create meassure model
			$model = new Models\Pressure_meassure_model($this->db);
			//get meassure id
			$id = $req->getAttribute('id');
			//query and encode
			$response = json_encode($model->getPatientmeassures($id));
			//make response
			$res->getBody()
				->write($response);

			return $res;
		});

		$this->get('/branch/{id}', function($req, $res, $args){
			//create patient model
			$patient_model = new Models\Pressure_patient_model($this->db);
			//meassure model
			$meassure_model = new Models\Pressure_meassure_model($this->db);
			//get branch id
			$branch_id = $req->getAttribute('id');
			//consult response
			$patient_res = $patient_model->getBranchPatients($branch_id);
			//meassures result
			$meassures = array();
			//if error is false
			if($patient_res->response){
				//rows patient
				foreach ($patient_res->getResult() as $patient) {
					$meassure_res = $meassure_model->getPatientMeassures($patient['id']);

					if($meassure_res->response == true){
						foreach ($meassure_res->getResult() as $meassure) {
							array_push($meassures, $meassure);
						}

						$response = new Response();
						$response->setResponse(true);
						$response->result = $meassures;


						return $res->getBody()
						   ->write(json_encode($response));
					}else{
						return $res->getBody()
						   ->write(json_encode($meassure_res));
					}
				}				
			}else{
				return $res->getBody()
						   ->write(json_encode($patient_res));
			}
		});
	});

	$this->group('/patient', function(){

		$this->get('/all', function($req, $res, $args){
			// Create patient model
			$model = new Models\Pressure_patient_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res->getBody()
				->write($response);
		});

		//Add new patient
		$this->post('/save', function($req, $res, $args){
			// Create patient model
			$model = new Models\Pressure_patient_model($this->db);
			// get data post
			$json_data = $req->getParsedBody();
			$data = json_decode($json_data['data'], true);
			// Validate fields
			if(isset($data['id'])){ // Update acction
				$validation_result = $this->validator->verifyRequiredParams(array('branch_id','name','document','age','gender','email','pass'), $data);
			}else{ // INsert action
				$validation_result = $this->validator->verifyRequiredParams(array('branch_id','name','document','age','gender'), $data);
			}
			
			
			if($validation_result->response){
				// Query database and json encode
				$response = json_encode($model->insertOrUpdate($data));
				//make response
				$res->getBody()
					->write($response);
			}else{
				$response = json_encode($validation_result);
				$res->getBody()
					->write($response);
			}

			return $res;
		});

		//Delete a patient
		$this->post('/delete/{id}', function($req, $res, $args){
			//create patient model
			$model = new Models\Pressure_patient_model($this->db);
			//get patient id
			$id = $req->getAttribute('id');
			//Query and encode
			$response = json_encode($model->delete($id));
			//make response
			$res->getBody()
				->write($response);

			return $res;
		});

		$this->get('/branch/{id}', function($req, $res, $args){
			//create patien model
			$model = new Models\Pressure_patient_model($this->db);
			//get branch id
			$id = $req->getAttribute('id');
			//query and enconde
			$response = json_encode($model->getBranchPatients($id));
			//make response
			$res->getBody()
				->write($response);

			return $res;
		});

		$this->get('/company/{id}', function($req, $res, $args){
			//create patient model
			$branch_model = new Models\Pressure_branch_model($this->db);
			//meassure model
			$patient_model = new Models\Pressure_patient_model($this->db);
			//get company id
			$company_id = $req->getAttribute('id');
			//consult response
			$branch_res = $branch_model->getCompanyBranches($company_id);
			//patients result
			$patients = array();
			//if error is false
			if($branch_res->response == true){
				//rows patient
				foreach ($branch_res->getResult() as $branch) {
					$patient_res = $patient_model->getBranchPatients($branch['id']);

					if($patient_res->response == true){
						foreach ($patient_res->getResult() as $patient) {
							array_push($patients, $patient);
						}

						$response = new Response();
						$response->setResponse(true);
						$response->result = $patients;


						return $res->getBody()
						   ->write(json_encode($response));
					}else{
						return $res->getBody()
						   ->write(json_encode($patient_res));
					}
				}	
			}else{
				return $res->getBody()
						   ->write(json_encode($branch_res));
			}
		});
	});

	$this->group('/branch', function(){

		//Get All Branches
		$this->get('/all', function($req, $res, $args){
			// Create branch model
			$model = new Models\Pressure_branch_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res->getBody()
				->write($response);
		});

		//Add new branch
		$this->post('/save', function($req, $res, $args){
			// Create branch model
			$model = new Models\Pressure_branch_model($this->db);
			// get data post
			$json_data = $req->getParsedBody();
			$data = json_decode($json_data['data'], true);

			$validation_result = $this->validator->verifyRequiredParams(array('company_id','host_id','name','nit','phone'), $data);
			
			if($validation_result->response){
				// Query database and json encode
				$response = json_encode($model->insertOrUpdate($data));
				//make response
				$res->getBody()
					->write($response);
			}else{
				$response = json_encode($validation_result);
				$res->getBody()
					->write($response);
			}
			return $res;
		});
	});

	$this->group('/company', function(){
		//Get all companies
		$this->get('/all', function($req, $res, $args){
			// Create company model
			$model = new Models\Pressure_company_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res->getBody()
				->write($response);
		});

		//Add new company
		$this->post('/save', function($req, $res, $args){
			// Create company model
			$model = new Models\Pressure_company_model($this->db);
			// get data post
			$json_data = $req->getParsedBody();
			$data = json_decode($json_data['data'], true);
			//Validate fields
			if(isset($data['id'])){
				$validation_result = $this->validator->verifyRequiredParams(array('name','nit','phone'), $data);
			}else{
				$validation_result = $this->validator->verifyRequiredParams(array('admin_id','name','nit','phone'), $data);
			}
			
			if($validation_result->response){
				// Query database and json encode
				$response = json_encode($model->insertOrUpdate($data));
				//make response
				$res->getBody()
					->write($response);
			}else{
				$response = json_encode($validation_result);
				$res->getBody()
					->write($response);
			}
			return $res;
		});
	});

	$this->group('/host', function(){
		//Get all hosts
		$this->get('/all', function($req, $res, $args){
			// Create host model
			$model = new Models\Pressure_company_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res->getBody()
				->write($response);
		});

		//Add new company
		$this->post('/save', function($req, $res, $args){
			// Create host model
			$model = new Models\Pressure_company_model($this->db);
			// get data post
			$json_data = $req->getParsedBody();
			$data = json_decode($json_data['data'], true);
			//Validate fields
			$validation_result = $this->validator->verifyRequiredParams(array('type','url','db','user','pass'), $data);

			if($validation_result->response){
			// Query database and json encode
			$response = json_encode($model->insertOrUpdate($data));
			//make response
			$res->getBody()
				->write($response);
			}else{
				$response = json_encode($validation_result);
				$res->getBody()
					->write($response);
			}

			return $res;
		});
	});

	$this->group('/admin', function(){
		//Get all admin
		$this->get('/all', function($req, $res, $args){
			// Create admin model
			$model = new Models\Pressure_admin_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res->getBody()
				->write($response);
		});

		//Add new admin
		$this->post('/save', function($req, $res, $args){
			// Create admin model
			$model = new Models\Pressure_admin_model($this->db);
			// get data post
			$json_data = $req->getParsedBody();
			$data = json_decode($json_data['data'], true);

			//Validate fields
			$validation_result = $this->validator->verifyRequiredParams(array('branch_id','name','document','age','gender','email','pass'), $data);

			if($validation_result->response){
				// Query database and json encode
				$response = json_encode($model->insertOrUpdate($data));
				//make response
				$res->getBody()
					->write($response);
			}else{
				$response = json_encode($validation_result);
				$res->getBody()
					->write($response);
			}
			return $res;
		});
	});
});