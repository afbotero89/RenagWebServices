<?php

namespace App\v2\src\routes;

use App\v2\src\models\pressure as Models;
use App\v2\src\libs\Response as Response;

// Routes
$app->group('/pressure', function(){

	//Patients routes
	$this->group('/patient', function(){

		$this->get('/all', function($req, $res, $args){
			// Create patient model
			$model = new Models\Pressure_patient_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res = $res->withHeader('Content-type', 'application/json');
			$res->getBody()
				->write($response);

			return $res;
		});



		$this->post('/new', function($req, $res, $args){
			$test = $req->getParsedBody();

			$response = new Response();
			$response->result = 1;
			$response->setResponse(true, $test['name']);

			$response = json_encode($response);
			//make response
			$res->getBody()
				->write($response);

				return $res;
		});
	});

	//Admin routes
	$this->group('/admin', function(){

		$this->get('/all', function($req, $res, $args){
			// Create patient model
			$model = new Models\Pressure_admin_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res = $res->withHeader('Content-type', 'application/json');
			$res->getBody()
				->write($response);

			return $res;
		});
	});

	//Test routes
	$this->group('/test', function(){

		$this->get('/all', function($req, $res, $args){
			// Create test model
			$model = new Models\Pressure_test_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res = $res->withHeader('Content-type', 'application/json');
			$res->getBody()
				->write($response);

			return $res;
		});
	});

	//Measure routes
	$this->group('/measure', function(){

		$this->get('/all', function($req, $res, $args){
			// Create measure model
			$model = new Models\Pressure_test_model($this->db);
			// Query database and json encode
			$response = json_encode($model->getAll());
			//make response
			$res = $res->withHeader('Content-type', 'application/json');
			$res->getBody()
				->write($response);

			return $res;
		});
	});
});